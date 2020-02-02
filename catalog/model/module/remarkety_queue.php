<?php
class ModelModuleRemarketyQueue extends Model
{
    const API_EVENTS_URL = 'https://api-events.remarkety.com/v1';
    const API_EVENTS_PORT = 443;

    const API_EVENTS_PLATFORM = 'OPENCART';
    const REMARKETY_MODULE_VERSION = 'oc23.1.1.16';

    public function createQueueRequest($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "remarkety_queue
            SET event_type = '" . $data['event_type'] . "',
                attempts = '" . (int)$data['attempts'] . "',
                last_attempt = '" . $data['last_attempt'] . "',
                next_attempt = '" . $data['next_attempt'] . "',
                data = '" . $data['data'] . "'");
    }

    public function updateQueueRequest($data)
    {
        if (isset($data['queue_id']) && count($data) > 1) {
            $fieldsSql = array();
            foreach ($data as $key => $value) {
                if ('queue_id' != $key) {
                    $fieldsSql[] = $key . " = '" . $value . "' ";
                }
            }
            $this->db->query("UPDATE " . DB_PREFIX . "remarkety_queue SET " . join(', ', $fieldsSql) . " WHERE queue_id = " . (int)$data['queue_id']);
        }
    }

    public function deleteQueue($queueId)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "remarkety_queue WHERE queue_id = '" . (int)$queueId . "'");
    }

    public function runQueue($queueId = null)
    {
        if ($queueId) {
            $sql = sprintf("
                SELECT *
                FROM " . DB_PREFIX . "remarkety_queue
                WHERE queue_id = %s
                ORDER BY next_attempt ASC
            "
                , $queueId);
        } else {
            $nextAttempt = date("Y-m-d H:i:s");

            $sql = sprintf("
                    SELECT *
                    FROM " . DB_PREFIX . "remarkety_queue
                    WHERE next_attempt <= '%s'
                    AND status = 1
                    ORDER BY next_attempt ASC
                "
                , $nextAttempt);
        }

        $query = $this->db->query($sql);

        foreach ($query->rows as $row) {
            $this->_resend($row, ($queueId) ? true : false);
        }
    }

    protected function _resend($row, $manually)
    {
        $result = $this->sendEventRequest($row['event_type'], $row['data'], ($row['attempts']+1), $row['queue_id'], $manually);
        if($result) {
            $this->deleteQueue($row['queue_id']);
        }
    }

    public function sendEventRequest($eventType, $data, $attempt = 1, $queueId = null, $manually = false)
    {
        $this->remarketyLog = new Log('remarkety.log');

        try {
            $timeout = 10;
            $maxredirects = 5;

            if ($queueId) {
                $jsonBody = $data;
            } else {
                $currentTimezone = date_default_timezone_get();
                date_default_timezone_set('UTC');
                $bodyBase = array(
                    'timestamp' => (string)time(),
                    'event_id' => $eventType,
                );
                date_default_timezone_set($currentTimezone);
                $jsonBody = json_encode(array_merge($bodyBase, $data));
            }

            $headers = $this->_getEventHeaders($eventType, $jsonBody);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_PORT, intval(self::API_EVENTS_PORT));
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($curl, CURLOPT_MAXREDIRS, $maxredirects);

            curl_setopt($curl, CURLOPT_URL, self::API_EVENTS_URL);
            curl_setopt($curl, CURL_HTTP_VERSION_1_1, true);
            curl_setopt($curl, CURLOPT_POST, true);

            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonBody);

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, true);

            $response = curl_exec($curl);
            $result = $this->_getResponse($response, $curl);

            if ('200' == $result['status_code'] && empty($result['error'])) {
                $this->remarketyLog->write(sprintf('Event request OK: %s, %s', $eventType , $jsonBody));
                return true;
            } else {
                $this->remarketyLog->write(sprintf('Event request Failed: %s, %s, %s', $eventType, $result['error'] , $jsonBody));
                $this->_queueRequest($eventType, $jsonBody, $attempt, $queueId, $manually);
            }

        } catch (Exception $e) {
            $this->remarketyLog->write(sprintf('Event request Error: %s, %s, %s', $eventType, $e->getMessage() , $jsonBody));
            $this->_queueRequest($eventType, $jsonBody, $attempt, $queueId, $manually);
        }
        return false;
    }

    protected function _getEventHeaders($eventType, $body)
    {
        $host = str_replace(array('https://', 'http://'), '', self::API_EVENTS_URL);
        $host = substr($host, 0, strpos($host, '/'));
        $headers = array(
            'Host: ' . $host,
            'Connection: close',
            'Accept-encoding: gzip, deflate',
            'Content-Type: application/json',
            'X-Domain: ' . str_replace(array('http://','https://'), '', $this->config->get('config_url')),
            'X-Token: ' . $this->config->get('remarkety_api_key'),
            'X-Event-Type: ' . $eventType,
            'X-Platform: ' . self::API_EVENTS_PLATFORM,
            'X-Version: ' . self::REMARKETY_MODULE_VERSION,
            'X-API-Version: 0.9',
            'Content-Length: ' . strlen($body)
        );

        return $headers;
    }

    protected function _getResponse($response_str, $curl)
    {
        $result = array(
            'status_code' => curl_getinfo($curl, CURLINFO_HTTP_CODE)
        );
        $parts = preg_split('|(?:\r?\n){2}|m', $response_str, 2);
        if (isset($parts[1])) {
            return array_merge($result, (array)json_decode($parts[1]));
        }
        return $result;
    }

    protected function _queueRequest($eventType, $body, $attempt, $queueId, $manually = false)
    {
        $intervals = explode(',',$this->config->get('remarkety_queue_intervals'));

        $now = time();
        if(!empty($intervals[$attempt-1])) {
            $nextAttempt = $now + (int)$intervals[$attempt-1] * 60;
            if($queueId) {
                $this->updateQueueRequest(array(
                    'queue_id' => $queueId,
                    'attempts' => $attempt,
                    'last_attempt' => date("Y-m-d H:i:s", $now),
                    'next_attempt' => date("Y-m-d H:i:s", $nextAttempt)
                ));

            } else {
                $this->createQueueRequest(array(
                    'event_type' => $eventType,
                    'attempts' => $attempt,
                    'last_attempt' => date("Y-m-d H:i:s", $now),
                    'next_attempt' => date("Y-m-d H:i:s", $nextAttempt),
                    'data' => $body
                ));
            }

        } elseif($queueId) {
            if ($manually) {
                $this->updateQueueRequest(array(
                    'queue_id' => $queueId,
                    'attempts' => $attempt,
                    'last_attempt' => date("Y-m-d H:i:s", $now),
                ));
            } else {
                $this->updateQueueRequest(array(
                    'queue_id' => $queueId,
                    'status' => 0,
                ));
            }
        }
    }
}
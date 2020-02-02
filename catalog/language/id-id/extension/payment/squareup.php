<?php
// Text
$_['text_new_card']                     = '+ Tambahkan kartu baru';
$_['text_loading']                      = 'Memuat ... Mohon tunggu ...';
$_['text_card_details']                 = 'Detail kartu';
$_['text_saved_card']                   = 'Gunakan Kartu Tersimpan:';
$_['text_card_ends_in']                 = 'Bayar dengan %s kartu yang ada akhiran XXXX XXXX XXXX %s';
$_['text_card_number']                  = 'Nomor kartu:';
$_['text_card_expiry']                  = 'Kadaluarsa kartu (MM/YY):';
$_['text_card_cvc']                     = 'Kode Keamanan Kartu (CVC):';
$_['text_card_zip']                     = 'Kode Pos Kartu:';
$_['text_card_save']                    = 'Simpan kartu untuk penggunaan selanjutnya?';
$_['text_trial']                        = '%s setiap %s %s untuk %s pembayaran itu';
$_['text_recurring']                    = '%s setiap %s %s';
$_['text_length']                       = ' untuk %s pembayaran';
$_['text_cron_subject']                 = 'Square CRON job summary';
$_['text_cron_message']                 = 'Berikut adalah daftar semua tugas CRON yang dilakukan oleh ekstensi Square Anda:';
$_['text_squareup_profile_suspended']   = ' Pembayaran rutin Anda telah ditangguhkan. Silahkan hubungi kami untuk lebih jelasnya.';
$_['text_squareup_trial_expired']       = ' Masa uji coba Anda telah habis masa berlakunya.';
$_['text_squareup_recurring_expired']   = ' Pembayaran rutin Anda telah habis masa berlakunya. Ini adalah pembayaran terakhirmu.';
$_['text_cron_summary_token_heading']   = 'Refresh token akses:';
$_['text_cron_summary_token_updated']   = 'Token akses berhasil diperbarui!';
$_['text_cron_summary_error_heading']   = 'Kesalahan Transaksi:';
$_['text_cron_summary_fail_heading']    = 'Transaksi Gagal (Profil Suspended):';
$_['text_cron_summary_success_heading'] = 'Transaksi yang berhasil:';
$_['text_cron_fail_charge']             = 'Profile <strong>#%s</strong> tidak dapat ditagih <strong>%s</strong>';
$_['text_cron_success_charge']          = 'Profile <strong>#%s</strong> Didakwa dengan <strong>%s</strong>';
$_['text_card_placeholder']             = 'XXXX XXXX XXXX XXXX';
$_['text_cvv']                          = 'CVV';
$_['text_expiry']                       = 'MM/YY';
$_['text_default_squareup_name']        = 'Credit / Debit Card';
$_['text_token_issue_customer_error']   = 'Kami mengalami pemadaman teknis dalam sistem pembayaran kami. Silakan coba lagi nanti.';
$_['text_token_revoked_subject']        = 'Your Square access token has been revoked!';
$_['text_token_revoked_message']        = "The Square payment extension's access to your Square account has been revoked through the Square Dashboard. You need to verify your application credentials in the extension settings and connect again.";
$_['text_token_expired_subject']        = 'Your Square access token has expired!';
$_['text_token_expired_message']        = "The Square payment extension's access token connecting it to your Square account has expired. You need to verify your application credentials and CRON job in the extension settings and connect again.";

// Error
$_['error_browser_not_supported']       = 'Error: The payment system no longer supports your web browser. Please update or use a different one.';
$_['error_card_invalid']                = 'Error: Card is invalid!';
$_['error_squareup_cron_token']         = 'Error: Access token could not get refreshed. Please connect your Square Payment extension via the OpenCart admin panel.';

// Warning
$_['warning_test_mode']                 = 'Warning: Sandbox mode is enabled! Transactions will appear to go through, but no charges will be carried out.';

// Statuses
$_['squareup_status_comment_authorized']    = 'The card transaction has been authorized but not yet captured.';
$_['squareup_status_comment_captured']      = 'The card transaction was authorized and subsequently captured (i.e., completed).';
$_['squareup_status_comment_voided']        = 'The card transaction was authorized and subsequently voided (i.e., canceled).   ';
$_['squareup_status_comment_failed']        = 'The card transaction failed.';

// Override errors
$_['squareup_override_error_billing_address.country']       = 'Payment Address country is not valid. Please modify it and try again.';
$_['squareup_override_error_shipping_address.country']      = 'Shipping Address country is not valid. Please modify it and try again.';
$_['squareup_override_error_email_address']                 = 'Your customer e-mail address is not valid. Please modify it and try again.';
$_['squareup_override_error_phone_number']                  = 'Your customer phone number is not valid. Please modify it and try again.';
$_['squareup_error_field']                                  = ' Field: %s';
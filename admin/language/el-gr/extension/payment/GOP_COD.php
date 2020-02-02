<?php
/**
 * @extension-payment	GOP_COD
 * @author-name			Michail Gkasios
 * @copyright			Copyright (C) 2013 GKASIOS
 * @license				GNU/GPL, see http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */

// Heading
$_['heading_title']						=	'GOP Cash On Delivery';

// Text
$_['text_edit']							=	'Επεξεργασία GOP Cash On Delivery';
$_['text_default']						=	'Προκαθορισμένο';
$_['text_payment']						=	'Πληρωμή';
$_['text_success']						=	'Επιτυχία: Τροποποιήσατε το extension πληρωμών GOP Cash On Delivery!';

// Tab
$_['tab_noshipping']					=	'Χωρίς Αποστολή';
$_['tab_general']						=	'Γενικά';
$_['tab_all_zones']						=	'Όλες οι Ζώνες';
$_['tab_extension']						=	'Extension';

// Entry
$_['entry_extension_status']			=	'Κατάσταση Extension';
$_['entry_extension_status_info']		=	'Ενεργοποιήστε/Απενεργοποιήστε αυτό το extension πληρωμών.';
$_['entry_status']						=	'Κατάσταση';
$_['entry_status_info']					=	'Ενεργοποιήστε/Απενεργοποιήστε αυτή την πληρωμή.';
$_['entry_shipping_geo_zone']			=	'Γεωγραφική Ζώνη Μεθόδου Αποστολής';
$_['entry_shipping_geo_zone_info']		=	'Ενεργοποιήστε/Απενεργοποιήστε την Γεωγραφική Ζώνη της Μεθόδου Αποστολής.';
$_['entry_sort_order']					=	'Σειρά Ταξινόμησης';
$_['entry_sort_order_info']				=	'Η σειρά ταξινόμησεις που θα εμφανίζετε η πληρωμή';
$_['entry_customer_group']				=	'Ομάδα Πελατών';
$_['entry_customer_group_info']			=	'Ομάδα Πελατών που επιθυμείτε να ισχύουν οι ρυθμίσεις.';
$_['entry_tax_class']					=	'Φορολογική Τάξη';
$_['entry_tax_class_info']				=	'Επιλέγοντας Φορολογική Τάξη θα προστεθούν οι ανάλογοι φόροι στο κόστος αυτής της μεθόδου πληρωμής.';
$_['entry_method']						=	'Μέθοδος';
$_['entry_method_info']					=	'Επιλέγξτε την μέθοδο χρέωσης της πληρωμής.';
$_['entry_flat_rate']					=	'Σταθερή Χρέωση';
$_['entry_flat_rate_info']				=	'Θέστε ένα σταθερό ποσό χρημάτων που θα χρεώνετε μη συμπεριλαμβανομένων των φόρων εάν έχετε επιλεξει Φορολογική Τάξη.';
$_['entry_percentage']					=	'Ποσοστιαία';
$_['entry_percentage_info']				=	'Βασιζόμενοι στο συνολικό κόστος της παραγγελείας χρεώστε ενά ποσοστό αυτού.';
$_['entry_custom']						=	'Προσαρμοσμένη';
$_['entry_custom_info']					=	'Μπορείτε να προσαρμόσετε αυτό το extension πληρωμών προσθέτοντας τον δικό σας php κώδικα.';
$_['entry_enable_rule']					=	'Κανόνας Ενεργοποίησης';
$_['entry_enable_rule_info']			=	'Μπορείτε να προσθέσετε έναν κανόνα για να Ενεργοποιήτε ή Απενεργοποιήτε το Extension.';
$_['entry_order_status']				=	'Κατάσταση Παραγγελίας';
$_['entry_order_status_info']			=	'Την κατάσταση που θα πάρει η παραγγελία αφού γίνει η επιβαιβαίωση από τον πελάτη.';
$_['entry_order_total']					=	'Σύνολο Παραγγελίας';
$_['entry_order_total_info']			=	'Ενεργοποιήστε/Απενεργοποιήστε το σύνολο της παραγγελίας.';
$_['entry_order_total_sort_order']		=	'Σειρά Ταξινόμησης Συνόλου Παραγγελίας';
$_['entry_order_total_sort_order_info']	=	'Η σειρά ταξινόμησεις που θα εμφανίζετε το σύνολο της παραγγελίας.';

// Error
$_['error_permission']					=	'Προσοχή: Δεν έχετε δικαίωμα να τροποποιήσετε τις πληρωμές μέσω Cash On Delivery!';
$_['error_number']						=	'Πρέπει να είναι αριθμός.';

// Info
$_['credits']							=	'Περί:';
$_['credits_info']						=	'<a onclick="window.open(\'http://www.gkasios.com\');"><img src="view/image/extension/payment/GKASIOS_Logo.png" alt="GKASIOS" title="GKASIOS"></a>';
$_['license']							=	'Άδεια Χρήσης:';
$_['license_info']						=	'Παρέχεται από την GKASIOS κάτω από την άδεια GNU/GPL.<br /><span class="help">Με επιφύλαξη παντός δικαιώματος.</span>';
$_['donate']							=	'Δωρεά:';
$_['donate_info']						=	'<img border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt="Δωρεά" title="Δωρεά" onclick="submitPaypal()">';
?>
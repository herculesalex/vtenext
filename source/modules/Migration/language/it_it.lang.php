<?php
$mod_strings = array (
  'LBL_MIGRATE_INFO' => 'Inserisci i Valori per Migrare i Dati da <b><i> Sorgente </i></b> a <b><i> Attuale (Piu Recente) vtigerCRM </i></b>',
  'LBL_CURRENT_VT_MYSQL_EXIST' => 'L`attuale installazione MySQL di vTiger si trova su',
  'LBL_THIS_MACHINE' => 'Questo computer',
  'LBL_DIFFERENT_MACHINE' => 'Un altro computer',
  'LBL_CURRENT_VT_MYSQL_PATH' => 'Attuale percorso (path) MySQL di Vtiger',
  'LBL_SOURCE_VT_MYSQL_DUMPFILE' => 'Nome del Dump File del vTiger <b>Sorgente</b> ',
  'LBL_NOTE_TITLE' => 'Note:',
  'LBL_NOTES_LIST1' => 'Se il MySQL Attuale si trova stessa macchina inserisci il path MySQL,  oppure specifica il Dump file se lo hai.',
  'LBL_NOTES_LIST2' => 'Se il MySQL Attuale si trova su un`altra Macchina inserisci il nome file di Dump (Sorgente) specificando il percorso completo.',
  'LBL_NOTES_DUMP_PROCESS' => 'Per estrarre il dump del Database esegui i seguenti comandi da dentro la cartella mysql/bin (cioe` dalla directory dove risiedono i binari di MySQL<br><b>mysqldump --user=\"mysql_username\" --password=\"mysql-password\" -h \"hostname\" --port=\"mysql_port\" \"database_name\" > nomefile_dump </b><br> aggiungi <b>SET FOREIGN_KEY_CHECKS = 0; </b> all`inizio del file di dump e aggiungi <b>SET FOREIGN_KEY_CHECKS = 1;</b> alla fine del file di dump',
  'LBL_NOTES_LIST3' => 'IIndica il percorso di MySQL nel formato <b>/home/crm/vtigerCRM4_5/mysql</b>',
  'LBL_NOTES_LIST4' => 'Indica il nome del file di dump con il percorso completo, come <b>/home/fullpath/4_2_dump.txt</b>',
  'LBL_CURRENT_MYSQL_PATH_FOUND' => 'Il percorso MySQL dell`installazione Attuale e` stato trovato.',
  'LBL_SOURCE_HOST_NAME' => 'Nome macchina Sorgente',
  'LBL_SOURCE_MYSQL_PORT_NO' => 'Porta MySql macchina sorgente :',
  'LBL_SOURCE_MYSQL_USER_NAME' => 'Nome utente MySql macchina Sorgente:',
  'LBL_SOURCE_MYSQL_PASSWORD' => 'Password MySql macchina Sorgente:',
  'LBL_SOURCE_DB_NAME' => 'Nome database MySql macchina Sorgente:',
  'LBL_MIGRATE' => 'Migra alla versione Attuale',
  'LBL_UPGRADE_VTIGER' => 'Aggiorna il Database di vTiger CRM ',
  'LBL_UPGRADE_FROM_VTIGER_423' => 'Aggiorna il DataBase da vTiger CRM 4.2.3 alla versione  5.0.0',
  'LBL_SETTINGS' => 'Impostazioni',
  'LBL_STEP' => 'Passo',
  'LBL_SELECT_SOURCE' => 'Seleziona Fonte',
  'LBL_STEP1_DESC' => 'Per iniziare la migrazione del DataBase, devi specificare il formato nel quale il vecchio database e` disponibile',
  'LBL_RADIO_BUTTON1_TEXT' => 'Ho accesso al sistema database live di vtiger ',
  'LBL_RADIO_BUTTON1_DESC' => 'Questa opzione richiede che tu abbia l`indirizzo della macchina host (dove il  DB risiede) e le credenziali di accesso al DB. Sia il sistema locale che remoti sono supportati con questo metodo. Fai riferimento alla documentazione per ulteriori informazioni.',
  'LBL_RADIO_BUTTON2_TEXT' => 'Ho accesso ad un dump archiviato di un database di vtiger CRM',
  'LBL_RADIO_BUTTON2_DESC' => 'Questa opzione richiede che il dump del database sia disponibile localmente, sulla stessa macchina su cui stai aggiornando. Non puoi accedere al dump del database da una macchina differente (database server remoto). Fai riferimento alla documentazione per ulteriori informazioni.',
  'LBL_RADIO_BUTTON3_TEXT' => 'Ho un database nuovo con i dati della versione 4.2.3',
  'LBL_RADIO_BUTTON3_DESC' => 'Questa opzione richiede i dettagli database vtiger CRM 4.2.3, incluso database server ID, user name, e password. Non puoi accedere al database dump da una macchina differente (database server remoto)',
  'LBL_HOST_DB_ACCESS_DETAILS' => 'Dettagli accesso database host',
  'LBL_MYSQL_HOST_NAME_IP' => 'MySQL Host Name o Indirizzo IP : ',
  'LBL_MYSQL_PORT' => 'MySQL Numero di Porta : ',
  'LBL_MYSQL_USER_NAME' => 'MySql User Name : ',
  'LBL_MYSQL_PASSWORD' => 'MySql Password : ',
  'LBL_DB_NAME' => 'Nome Database : ',
  'LBL_LOCATE_DB_DUMP_FILE' => 'Specifica il database dump file',
  'LBL_DUMP_FILE_LOCATION' => 'Posizione del File di Dump: ',
  'LBL_RADIO_BUTTON3_PROCESS' => '<font color=\\\\\"red\\\\\">Non specificare i dettagli del database 4.2.3. Questa opzione modifichera` direttamente e permanentemente il database selezionato.</font>. E` fortemente consigliato di fare un dump del database 4.2.3, creare un nuovo database, e applicare al nuovo database il dump del database 4.2.3. Questa migrazione modifica il database per farlo corrispondere allo schema della versione 5.0',
  'LBL_ENTER_MYSQL_SERVER_PATH' => 'Inserisci il percorso del Server MySQL',
  'LBL_SERVER_PATH_DESC' => 'Percorso dell`installazione MySQL, es. <b>/home/5beta/vtigerCRM5_beta/mysql/bin</b> or <b>c:\\Programmi\\mysql\\bin</b>',
  'LBL_MYSQL_SERVER_PATH' => 'Percorso Server MySQL : ',
  'LBL_MIGRATE_BUTTON' => 'Migra',
  'LBL_CANCEL_BUTTON' => 'Annulla',
  'LBL_UPGRADE_FROM_VTIGER_5X' => 'Aggiorna il database da vTiger 5.x a una versione successiva',
  'LBL_PATCH_OR_MIGRATION' => 'devi specificare la versione del database di origine (aggiornamento da Patch o Migrazione)',
  'ENTER_SOURCE_HOST' => 'Prego inserire il nome Host di origine',
  'ENTER_SOURCE_MYSQL_PORT' => 'Prego inserire la porta MySql di origine',
  'ENTER_SOURCE_MYSQL_USER' => 'Prego inserire l`utente MySql di origine',
  'ENTER_SOURCE_DATABASE' => 'Prego inserire il Database  MySql di origine',
  'ENTER_SOURCE_MYSQL_DUMP' => 'Prego inserire un file di dump Mysql valido',
  'ENTER_HOST' => 'Prego inserire il nome Host',
  'ENTER_MYSQL_PORT' => 'Prego inserire la porta MySql',
  'ENTER_MYSQL_USER' => 'Prego inserire l`utente MySql',
  'ENTER_DATABASE' => 'Prego inserire il nome del Database',
  'SELECT_ANYONE_OPTION' => 'Prego selezionare un`opzione',
  'ENTER_CORRECT_MYSQL_PATH' => 'Prego inserire il percorso MySql corretto',
'CHK1'=>'Prego fornire i permessi di lettura/scrittura alla cartella user_privileges.',
'CHK2'=>'Il file di dump di Mysql non esiste nel percorso specificato',
'CHK3'=>'  Non e\' stato possibile connettersi al server del database sorgente',
'CHK4'=>'  Non e\' stato possibile connettersi al server del database corrente',
'CHK5'=>' Il database sorgente non esiste',
'CHK6'=>' Le tabelle non esistono nel database sorgente',
'CHK7'=>' Entrambi i database sono identici.',
'CHK8'=>' Non e\'stato possibile creare una connessione con le correnti impostazioni del database.',
'CHK9'=>'ERRORE!!!!!Prego controllare i valori in input, impossibile continuare.',
'CHK10'=>'Il file caricato eccede la massima dimensione consentita.Prova altre opzioni.',
'CHK11'=>'Prego inserire un file di dump valido.',
'MIGR_COMPLETE'=>'La migrazione e\' stata completata',
'OLD_DATA_COMPLETE'=>'I tuoi vecchi dati sono stati importati nel nuovo vtiger CRM',

'INFO1'=>'Migrazione da una versione precedente',
'INFO2'=>'Aggiorna il database del nuovo vtiger CRM 5 con i dati della precedente installazione',
'INFO3'=>'Per iniziare, segui le istruzioni che seguono',
'INFO4'=>'Risultati migrazione',
'INFO5'=>' La migrazione e\' in corso. Attendere prego...',
'INFO6'=>'I tuoi vecchi dati vengono importati nel nuovo vtiger CRM',
'INFO7'=>'Destinatione del database corrente',
'INFO8'=>'Nome host ',
'INFO9'=>'Numero della porta di MySQL',
'INFO10'=>'Nome utente MySQL',
'INFO11'=>'Password MYSQL',
'INFO12'=>'Nome del DB',
'INFO13'=>'Database sorgente',
'INFO14'=>'Numero porta di MySQL',
'INFO15'=>'Nome utente MySQL',
'INFO16'=>'Password di MYSQL',
'INFO17'=>'Log del processo di migrazione',
'INFO18'=>'Log delle query di migrazione',
'INFO19'=>'Le query che seguono sono eseguite per portare il database dalla versione 4.2.3/4.2 Patch2 alla 5.0.',
'INFO20'=>'Stato oggetto',
'INFO21'=>'Successo (S) / Fallito (F)',
'INFO22'=>'Log delle query fallite',
'INFO23'=>'Numero complessivo delle query eseguite :',
'INFO24'=>'Query eseguite con successo : ',
'INFO25'=>'Query fallite : ',
'INFO26'=>'Nota:  Per favore copia e archivia le query fallite, puo\' servire per capire eventuali errori nell\'utilizzo della nuova versione.',

'MIGR1'=>'E\' stato eseguito il dump del database e il file e\'',
'MIGR2'=>'Il database e\' stato cancellato.',
'MIGR3'=>'Il database e\' stato creato.',
'MIGR4'=>'Il dump del Database e\' stato applicato al database ',
'MIGR5'=>'dal file ',
'MIGR6'=>'Il database corrente sta per essere modificato con l\'esecuzione delle seguenti query...',
'MIGR7'=>'Dump del database sorgente eseguito correttamente.',
'MIGR8'=>'Il dump del database sorgente puo\' non contenere tutti i valori. Prego utilizzare altre opzioni.',
'MIGR9'=>'Dump del database corrente eseguito correttamente.',
'MIGR10'=>'Il dump del database corrente puo\' non contenere tutti i valori. Se ci fossero problemi nella migrazione non si potrebbe ristabilire il database. Se la migrazione non si completasse, rinomina il file install.php e la cartella install e lancia il file install.php',
'MIGR11'=>'I database sono uguali, salta il processo di dump e crea il database corrente.',
'MIGR12'=>'I database sono diversi. Cancella il database corrente e crea il nuovo. Inoltre applica il dump alvecchio database',
'MIGR13'=>'Il dump non e\' stato applicato correttamente. Le tabelle esistenti nel database 4.2.3 sono :',
'MIGR14'=>'Le tabelle esistenti nel database corrente dopo aver applicato il dump sono :',
'MIGR15'=>'Il dump non puo\' essere applicato correttamente. Il database e\' stato riportato alla versione precedente.',

'STEP01'=>'Per avere il supporto completo al charset UTF-8 :- <ol><li>Setta la linea $default_charset=\'UTF-8\'; al posto di quella esistente nel config.inc.php nella root di vtiger</li><li>Una volta fatto, dopo aver ricaricato la pagina seleziona il checkbox qui sotto per la conversione dei dati nel database nel nuovo charset.</li></ol>',
'STEP02'=>'Per continuare senza il supporto al charset UTF-8, lascia l\'opzione deselezionata.',
'STEP03'=>'Per avere il supporto completo UTF-8, raccomandiamo di settare la linea \$default_charset=\'UTF-8\'; al posto di quella esistente nel config.inc.php nella root di vtiger.',
'STEP04'=>'Selezione il check box sottostante dopo aver cambiato il file di configurazione, per avere il supporto all\'UTF-8 (Unicode support).',
'STEP05'=>'UTF-8 dovrebbe essere abilitato nel database per avere un supporto completo, verra\' utilizzato nella conversione dei dati',
'STEP06'=>'Deseleziona il check box sottostante, se non hai bisogno della conversione dei dati al formato UTF-8.',
'STEP07'=>'Prego cambiare il file di configurazione (situato nella root di vtiger, con il nome config-inc.php) per il supporto all\'UTF-8 e poi aggiorna la pagina',
'STEP08'=>'Prego riferirsi a',
'STEP09'=>'queste note di migrazione',
'STEP10'=>'prima di procedere oltre.',
'STEP11'=>'I cambiamenti fatti durante la migrazione non sono reversibili.E\' caldamente raccomandato di fare un dump del database prima della migrazione. ',
'STEP12'=>'inlotre e\' raccomandato procedere con la migrazione nel seguente modo: ',
'STEP13'=>'Fare un dump del database corrente (il vecchio database dal quale si vuole migrare). ',
'LBL_HELP'=>'Aiuto',
'STEP14'=>'Editare il file didump, cerca e sostituisci la stringa "latin1" con "utf8" in tutti le occorrenze, per esempio, dobbiamo trovare la stringa CHARSET=latin1 e rimpiazzarla con CHARSET=utf8 che appare in ogni occorrenza di creazione (CREATE) sql',
'STEP15'=>'Crea un nuovo database con il set di caratteri (charset) predefinito utf8.',
'STEP16'=>'Applicare il dump su questo database appena creato.',
'STEP17'=>'Cambiare il parametro "dbname" nel config-inc.php (nella root di vtiger) inserendo al posto del database corrente, quello appena creato',
'STEP18'=>'Ora iniziare la migrazione (dalla versione attuale alla piu\' recente)',
'STEP19'=>'Come fare il dump del database?',
'STEP20'=>'Posizionarsi nella cartella mysql/bin da terminale (linux) o da prompt dei comandi (windows)',
'STEP21'=>'Eseguire i comandi seguenti per creare il dump',
'STEP22'=>'I dati di accesso del database MySQL si trovano nel file config.inc.php.',
'STEP23'=>'Come creare un database?',
'STEP24'=>'Andare nella cartella mysql/bin dal terminale (linux) o da linea di comando (windows)',
'STEP25'=>'Eseguire i comandi che seguono per creare un nuovo database',
'STEP26'=>'Il set di caratteri di default assegnato al database puo\' essere impostato direttamente come utf8 all\'atto della creazione grazie a questo comando:',
'STEP29'=>'Come importare i dati da un dump del database nel nuovo database appena creato?',
'STEP28'=>'Ulteriori informazioni per il supporto database UTF-8 si possono trovare nel seguente link ',
'STEP30'=>'Modifica il file di dump del database',
'STEP31'=>'aggiungere questa riga all\'inizio del file di dump.',
'STEP32'=>'aggiungere questa riga alla fine del file di dump.',
'STEP33'=>'Posizionarsi nella cartella mysql/bin da terminale (linux) o da linea di comando (windows) e assicurarsi che il file di dump del database si trovi in quel percorso.',
'STEP34'=>'Eseguire i comandi seguenti per applicare il dump del database sul nuovo database',
'STEP35'=>'Come utilizzare il nuovo database migrato?',
'STEP36'=>'Una volta fatta la migrazione, bisogna ripristinare le seguenti cartelle dalla vecchia installazione alla nuova',
'STEP37'=>'che contiene gli allegati',
'STEP38'=>'che contiene alcuni file immagine',
'STEP39'=>'che contiene i privilegi di accesso e altri file (numerazione preventivi, fatture ecc...)',
'STEP40'=>'set di caratteri vtigerCRM',
'STEP41'=>'set di caratteri del database',
'STEP42'=>'Non soddisfa i requisiti per il supporto UTF-8.',
'STEP43'=>'soddisfa i requisiti per il supporto UTF-8.',
'STEP44'=>'Converti i dati a UTF-8',
'HERE'=>'qui',
'STEP45'=>'Migrazione da vtiger',
'STEP46'=>'alla versione corrente',
'STEP47'=>'Questa opzione serve per passare da vtiger',
'VERSION'=>'',
'STEP48'=>'Questa opzione richiedera\' i dettagli del database che si potranno inserire nela prossimo step',
'STEP49'=>'La versione del database e dei file sorgenti sono le medesime. Non si puo\' procedere con la migrazione. Controllare il database e fare i passi necessari.',
'STEP50'=>'Seleziona la versione sorgente',
'STEP51'=>'Saranno applicati i cambiamenti del database tra la versione sorgente e la versione corrente',
'STEP52'=>'Versione di vtiger da migrare',
'STEP53'=>'Questa opzione applichera\' i cambiamenti del database da vtiger ',
);
?>
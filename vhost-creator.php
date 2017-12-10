<?php


$apachePort=":80";
$pathFolder="/home/mehrez/Bureau/backend-web/web/";
$serverName="archivage.dev";

/* si le path folder n'est pas sous /var/www on doit modifier la config apache /etc/apache2/apache2.conf 
<Directory /home/mehrez/>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
</Directory>
*/
/* ici on genere   ladresse ip locale  disponible   */
$arrayLigne = array();
            $handle = fopen("/etc/hosts", 'r');
            if ($handle) {
                $i = 0;               
                while (!feof($handle)) { 
                    $i = $i + 1;                    
                    $buffer = fgets($handle);                    
		    if (preg_match("/127/i",$buffer)) {
			 $buffer = intval(explode('.', $buffer)[3]);                      
		         $arrayLigne[$i] =  $buffer ;  
                    }
                }               
            fclose($handle); 
 }

$nextOne = max($arrayLigne)+1;
$ip="127.0.0.".$nextOne;

if ( !$ip ) {
   die(' internal error , try again'."\n"); 
}

$fichierconf =  sprintf("/etc/apache2/sites-available/%s.conf",$serverName);
$vhostStr = "<VirtualHost %s>"."\n".
	    "  ServerName %s"."\n".
	    " 	 DocumentRoot '%s'"."\n".
	    "  	 DirectoryIndex app_dev.php"."\n".
	    "  	 <Directory '%s'>"."\n".
	    "   		 DirectoryIndex app_dev.php"."\n".
	    "   		 AllowOverride All"."\n".
	    "   		 Allow from All"."\n".
	    " 	 </Directory>"."\n".
	    "</VirtualHost>"
	   ;
$vhostStr =  sprintf($vhostStr,$ip.$apachePort,$serverName,$pathFolder,$pathFolder);
/* ici on crée les fichiers conf sous site available et enabled + restart apache */
if(!is_file($fichierconf)){
        file_put_contents($fichierconf,$vhostStr);
		$shortConfFile = $serverName.".conf";
		$firstcmd ="sudo a2ensite ".$shortConfFile;
		exec($firstcmd); 
		$secondcmd ="sudo service apache2 restart";
		exec($secondcmd);
		$str = "%s	%s";
		$str =  sprintf($str,$ip,$serverName);
		$str= '"' . $str .'"';
		$hostfile="/etc/hosts";
		$thirdcmd= "sudo echo %s >> ".$hostfile;
		$thirdcmd =  sprintf($thirdcmd,$str);
		exec($thirdcmd);
}else{
	die(' vhost error maybe already exist '."\n");
}
echo " all right now , try with your browser => ".$serverName."\n";


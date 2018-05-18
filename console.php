<?php
// php console.php login password

include 'XpathHelper.php';
include 'LoginCrawler.php';


if(PHP_SAPI != 'cli'){
	die("Użyj CLI.\n");    	
} 
if(empty($argv[1]) && empty($argv[2])){
	die("Wyślij parametry login i password za pomocą CLI.\n");    	
} else {
	$login = !empty($argv[1]) ? trim($argv[1]): NULL;
	$password = !empty($argv[2]) ? trim($argv[2]): NULL;
} 

$twitter_crawler = new LoginCrawler('https', 'twitter.com');

echo "Wchodzę na stronę logowania\n";
$login_page = $twitter_crawler->get_login_page('/login');

$authenticity_token = XpathHelper::get_html_element('//input[@name=\'authenticity_token\']', $login_page['source']);
$authenticity_token = $authenticity_token[0]['value'];

echo "Śpię...\n";
sleep(3);
echo "Loguje się za pomocą podanych danych\n";
$login = $twitter_crawler->login('/sessions', $login, $password, $authenticity_token);

echo "Śpię...\n";
sleep(3);
echo "Wchodzę na stronę ustawień\n";
$settings = $twitter_crawler->redirect_after_login('/settings/account');
$user_email = XpathHelper::get_html_element('//input[@id=\'user_email\']', $settings['source']);
$user_time_zone = XpathHelper::get_html_element('//select[@id=\'user_time_zone\']/option[@selected]', $settings['source']);
if(!empty($user_email[0]) && !empty($user_email[0])){
	echo "Dane:\n";
	echo $user_email[0]['value']."\n";
	echo $user_time_zone[0]['value']."\n";
} else {
	die("Brak pól formularza - nie jesteś zalogowany.\n");
}

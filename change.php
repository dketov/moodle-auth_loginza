<?php
require('../../config.php');

$PAGE->set_url('/auth/loginza/change.php');
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
$PAGE->set_context($systemcontext);

$strforgotten = get_string('pluginname', 'auth_loginza') . ': ' . get_string('passwordforgotten');
$strlogin     = get_string('login');

$PAGE->navbar->add($strlogin, get_login_url());
$PAGE->navbar->add($strforgotten);
$PAGE->set_title($strforgotten);
$PAGE->set_heading($COURSE->fullname);

echo $OUTPUT->header();
echo $OUTPUT->box_start();
?>
<p>Вы зарегистрированы с использованием уже имеющейся у Вас учетной записи в одной из социальных сетей или почтовых систем: ВКонтакте, Facebook, Однокласники.ru, @mail.ru, Яндекс, Google...</p>
<p>Именно поэтому Ваш пароль НЕ хранится у нас, и его изменениe Вам необходимо сделать
в соотвтетствующей социальной сети или почтовой системе.</p>
<?php
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
?>

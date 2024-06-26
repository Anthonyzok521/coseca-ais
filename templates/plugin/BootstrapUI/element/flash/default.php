<?php
$class = array_unique((array)$params['class']);
$message = (isset($params['escape']) && $params['escape'] === false) ? $message : h($message);

if (in_array('alert-dismissible', $class)) {
    $button = <<<BUTTON
<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
BUTTON;
    $message = $button . $message;
}
if (is_array($class)) {
    $class = join(' ', $class);
}
echo $this->Html->div($class, $message, $params['attributes']);
//Set timeout to close the alert message. Callback, select button close, use click method. in 5 seconds?>
<script> setTimeout(() => document.querySelector("div[role='alert']>.close").click(), 5000);  </script>
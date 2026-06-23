<?php

\Kyzegs\Laracord\Client\Http::post(sprintf(\Kyzegs\Laracord\Constants\Routes::CREATE_GLOBAL_APPLICATION_COMMAND, $appId), ['name' => 'ping']);

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->commands()->createGlobalCommand(['application_id' => $appId], ['name' => 'ping']);

?>

<?php

\Kyzegs\Laracord\Client\Http::get(sprintf(\Kyzegs\Laracord\Constants\Routes::GET_GLOBAL_APPLICATION_COMMAND, $appId, $commandId));

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->commands()->getGlobalCommand(['application_id' => $appId, 'command_id' => $commandId]);

?>

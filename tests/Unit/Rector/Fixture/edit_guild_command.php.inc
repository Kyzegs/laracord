<?php

\Kyzegs\Laracord\Client\Http::patch(sprintf(\Kyzegs\Laracord\Constants\Routes::EDIT_GUILD_APPLICATION_COMMAND, $appId, $guildId, $commandId), ['description' => 'updated']);

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->commands()->editGuildCommand(['application_id' => $appId, 'guild_id' => $guildId, 'command_id' => $commandId], ['description' => 'updated']);

?>

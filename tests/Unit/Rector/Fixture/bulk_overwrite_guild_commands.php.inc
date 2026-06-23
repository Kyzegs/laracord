<?php

\Kyzegs\Laracord\Client\Http::put(sprintf(\Kyzegs\Laracord\Constants\Routes::BULK_OVERWRITE_GUILD_APPLICATION_COMMANDS, $appId, $guildId), $commands);

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->commands()->bulkOverwriteGuildCommands(['application_id' => $appId, 'guild_id' => $guildId], $commands);

?>

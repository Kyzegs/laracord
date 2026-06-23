<?php

\Kyzegs\Laracord\Client\Http::get(sprintf(\Kyzegs\Laracord\Constants\Routes::LIST_GUILD_MEMBERS, $guildId), ['limit' => 100]);

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->guilds()->listMembers(['guild_id' => $guildId], null, ['limit' => 100]);

?>

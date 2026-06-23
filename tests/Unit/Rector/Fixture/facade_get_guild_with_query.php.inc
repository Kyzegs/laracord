<?php

\Kyzegs\Laracord\Facades\Laracord::getGuild($guildId, ['with_counts' => true]);

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->guilds()->get(['guild_id' => $guildId], null, ['with_counts' => true]);

?>

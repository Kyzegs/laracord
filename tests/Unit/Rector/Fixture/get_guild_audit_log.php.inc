<?php

\Kyzegs\Laracord\Client\Http::get(sprintf(\Kyzegs\Laracord\Constants\Routes::GET_GUILD_AUDIT_LOG, $guildId));

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->auditLogs()->get(['guild_id' => $guildId]);

?>

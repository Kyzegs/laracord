<?php

\Kyzegs\Laracord\Client\Http::patch(sprintf(\Kyzegs\Laracord\Constants\Routes::MODIFY_CHANNEL, $channelId), ['name' => 'general']);

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->channels()->edit(['channel_id' => $channelId], ['name' => 'general']);

?>

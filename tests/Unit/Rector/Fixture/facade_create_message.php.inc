<?php

\Kyzegs\Laracord\Facades\Laracord::createMessage($channelId, ['content' => 'Hello']);

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->messages()->create(['channel_id' => $channelId], ['content' => 'Hello']);

?>

<?php

\Kyzegs\Laracord\Facades\Laracord::editMessage($channelId, $messageId, ['content' => 'Updated']);

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->messages()->edit(['channel_id' => $channelId, 'message_id' => $messageId], ['content' => 'Updated']);

?>

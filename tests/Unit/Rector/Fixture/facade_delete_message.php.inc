<?php

\Kyzegs\Laracord\Facades\Laracord::deleteMessage($channelId, $messageId);

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->messages()->delete(['channel_id' => $channelId, 'message_id' => $messageId]);

?>

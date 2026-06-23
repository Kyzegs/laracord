<?php

\Kyzegs\Laracord\Facades\Laracord::getChannel($channelId);

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->channels()->get(['channel_id' => $channelId]);

?>

<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->channels()->getChannel(['channel_id' => $channelId]);
\Kyzegs\Laracord\Facades\Laracord::withoutAuthentication();

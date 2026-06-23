<?php

\Kyzegs\Laracord\Client\Http::get(\Kyzegs\Laracord\Constants\Routes::GET_CURRENT_APPLICATION);

?>
-----
<?php

\Kyzegs\Laracord\Facades\Laracord::bot()->applications()->getCurrent();

?>

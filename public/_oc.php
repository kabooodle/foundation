<?php
highlight_string("<?php\n" . var_export(opcache_get_status(), true) . ";\n?>");
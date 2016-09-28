Your claimed item, {!! $item->name !!}, was rejected by the seller:<br>
{{ nl2br(e($claim->rejected_reason)) ? : '<em>No reason given</em>'  }}
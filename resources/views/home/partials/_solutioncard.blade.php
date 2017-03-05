<div class="card card-as-list no-border clearfix">
    <div class="card-icon">
        <img height="100" width="100" src="<?php echo staticAsset('/assets/images/home/icons/'.$_card['icon'].'') ?>">
    </div>
    <div class="card-block">
        <h4 class="card-title">{{ $_card['title'] }}</h4>
        <p class="card-text">{!! $_card['body']  !!}</p>
    </div>
</div>
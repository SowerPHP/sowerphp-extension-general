<h1><?=$title?></h1>
<div id="navpanel">
<?php foreach ($nav as $link=>&$info): ?>
    <div class="pull-left">
        <div class="icon">
            <a href="<?=$_base,'/',$module,$link?>" title="<?=$info['desc']?>">
                <img src="<?=$_base,$info['imag']?>" alt="<?=$info['name']?>" align="middle" />
                <span><?=$info['name']?></span>
            </a>
        </div>
    </div>
<?php endforeach; ?>
</div>

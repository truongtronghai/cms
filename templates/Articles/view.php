<h1><?php echo $article->title; ?></h1>
<p>
  <?= $article->body ?>
</p>
<p>
  <b><?= __("Tags:") ?></b> <?= $article->tag_string ?>
  
</p>
<div>
  <?= $this->Html->link('Edit',["controller"=>"articles","action"=>"edit",$article->slug],['class'=>'button']) ?>
  &nbsp;  &nbsp;  &nbsp;
  <?php echo $this->Html->link(__("Back"),['controller'=>'articles','action'=>'index'],['class'=>'button']) ; ?>
</div>

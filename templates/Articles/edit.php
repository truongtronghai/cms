<?= $this->Flash->render() ?>
<h1><?= __("Edit article") ?></h1>
<?php
  echo $this->Form->create($article);
  //echo $this->Form->control('user_id',['type'=>'hidden','value'=>1]);
  echo $this->Form->control('title');
  echo $this->Form->control('body',["row"=>'3']);
  echo $this->Form->control('tag_string', ['type' => 'text']);
  echo $this->Form->button(__("Save"));
  echo $this->Form->end();
 ?>
<?php echo $this->Html->link(__("Back"),['controller'=>'articles','action'=>'index']) ; ?>

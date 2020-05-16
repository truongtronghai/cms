<div class="users form">
  <?= $this->Flash->render() ?>
  <h3><?= __("Login") ?></h3>
  <?= $this->Form->create() ?>
  <fieldset>
    <legend>
      <?= __("Please enter your email and password") ?>
    </legend>
    <?= $this->Form->control('email',['required'=>true]) ?>
    <?= $this->Form->control('password',['required'=>true]) ?>
  </fieldset>
  <?= $this->Form->submit(__("Login")) ?>
  <?= $this->Form->end() ?>

  <?= $this->Html->link(__("Add user"),['controller'=>'users','action'=>'add']) ?>
</div>

<h2>Notifications</h2>

<?php foreach ($notifications as $n): ?>
<div class="notification-card">
  <h4><?= $n->title ?></h4>
  <p><?= $n->message ?></p>
  <small><?= date('M d, Y', strtotime($n->created_at)) ?></small>
</div>
<?php endforeach; ?>

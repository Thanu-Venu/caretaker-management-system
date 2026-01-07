<h2>Complaint #<?= $compl['complaint_id'] ?></h2>
<p><strong>Client:</strong> <?=htmlspecialchars($compl['client_name'])?></p>
<p><strong>Caretaker:</strong> <?=htmlspecialchars($compl['caretaker_name'])?></p>
<p><strong>Category:</strong> <?=htmlspecialchars($compl['category'])?></p>
<p><strong>Details:</strong><br><?=nl2br(htmlspecialchars($compl['details']))?></p>
<p><strong>Status:</strong> <?= $compl['status'] ?></p>
<p><strong>Assigned to:</strong> <?= htmlspecialchars($compl['assigned_to'] ?? '-') ?></p>

<hr>
<h3>Actions</h3>
<form action="/?route=complaints/assign" method="post" style="display:inline-block; margin-right:10px">
  <input type="hidden" name="id" value="<?= $compl['complaint_id'] ?>">
  <input name="investigator" placeholder="Investigator name">
  <button type="submit">Assign to Investigator</button>
</form>

<form action="/?route=complaints/resolve" method="post" style="display:inline-block">
  <input type="hidden" name="id" value="<?= $compl['complaint_id'] ?>">
  <button type="submit">Mark as Resolved</button>
</form>

<p><a href="/?route=complaints/edit&id=<?= $compl['complaint_id'] ?>">Edit</a> | <a href="/?route=complaints/index">Back</a></p>
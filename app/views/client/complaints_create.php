<h2>New Complaint</h2>
<form action="/?route=complaints/store" method="post">
  <label>Client Name: <input name="client_name" required></label><br><br>
  <label>Caretaker Name: <input name="caretaker_name" required></label><br><br>
  <label>Category:
    <select name="category">
      <option>Caretaker Behavior</option>
      <option>Service Quality</option>
      <option>Missed Visit</option>
      <option>Other</option>
    </select>
  </label><br><br>
  <label>Details:<br><textarea name="details" rows="6" cols="60" required></textarea></label><br><br>
  <button type="submit">Submit</button>
</form>


// Download CSV
document.getElementById("downloadReport").addEventListener("click", function() {
  let csvContent = "data:text/csv;charset=utf-8,";
  csvContent += "Client,Service,Date,Hours,Payment\n";
  services.forEach(s => {
    csvContent += `${s.client},${s.service},${s.date},${s.hours},${s.payment}\n`;
  });
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", "caretaker_report.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
});

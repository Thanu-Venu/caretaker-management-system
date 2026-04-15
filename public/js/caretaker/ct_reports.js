function escapeCsvValue(value) {
  const normalized = String(value ?? "");
  if (/[",\n]/.test(normalized)) {
    return `"${normalized.replace(/"/g, '""')}"`;
  }
  return normalized;
}

const downloadBtn = document.getElementById("downloadReport");
if (downloadBtn) {
  downloadBtn.addEventListener("click", function () {
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Client,Service,Date,Hours,Payment\n";

    (window.services || []).forEach((row) => {
      const line = [
        escapeCsvValue(row.client),
        escapeCsvValue(row.service),
        escapeCsvValue(row.date),
        escapeCsvValue(row.hours),
        escapeCsvValue(row.payment),
      ].join(",");
      csvContent += `${line}\n`;
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "caretaker_report.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });
}

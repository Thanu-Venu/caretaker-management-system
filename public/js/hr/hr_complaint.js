/**
 * HR complaints — tab switch (client vs caregiver).
 */
function switchComplaintTab(tabId, event) {
    document.querySelectorAll('.issuePanel').forEach(function (tab) {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.issueTab').forEach(function (btn) {
        btn.classList.remove('active');
    });

    var pane = document.getElementById(tabId);
    if (pane) {
        pane.classList.add('active');
    }
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }
}

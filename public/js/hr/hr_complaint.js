/**
 * HR complaints — tab switch (client vs caregiver).
 */
function switchComplaintTab(tabId, event) {
    document.querySelectorAll('.complaint-tab-panel').forEach(function (tab) {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.complaint-tab-btn').forEach(function (btn) {
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

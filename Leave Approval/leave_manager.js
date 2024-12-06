async function updateLeaveStatus(leave_id, action) {
    const manager_id = localStorage.getItem('user_id'); // Get manager ID from session storage
    const comment = document.getElementById(`comment-${leave_id}`).value; // Get comment

    const formData = new FormData();
    formData.append('action', action); // 'approve' or 'decline'
    formData.append('leave_id', leave_id);
    formData.append('user_id', manager_id);
    formData.append('comment', comment);

    const response = await fetch('../backend/leave_manager.php', {
        method: 'POST',
        body: formData
    });
    const data = await response.json();

    if (data.status === 'success') {
        alert(data.message);
        fetchLeaveRequests(); // Refresh leave requests to reflect changes
    } else {
        alert('Error: ' + data.message);
    }
}

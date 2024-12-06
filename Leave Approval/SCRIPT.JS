document.addEventListener("DOMContentLoaded", fetchRequests);

function fetchRequests() {
    fetch("http://localhost:3000/requests")
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("tbody");
            tbody.innerHTML = ""; // Clear existing rows

            data.forEach(request => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${request.username}</td>
                    <td>${new Date(request.start_date).toLocaleDateString()} - ${new Date(request.end_date).toLocaleDateString()}</td>
                    <td>${new Date(request.date_requested).toLocaleDateString()}</td>
                    <td>${request.leave_type}</td>
                    <td>
                        <button class="btn btn-decline" onclick="updateRequest(${request.leave_id}, false)">Decline</button>
                        <button class="btn btn-approve" onclick="updateRequest(${request.leave_id}, true)">Approve</button>
                    </td>
                    <td>
                        <input type="text" class="comment-input" id="comment-${request.leave_id}" placeholder="Add comment">
                    </td>
                `;
                tbody.appendChild(row);
            });
        });
}

function updateRequest(leaveId, isApproved) {
    const comment = document.getElementById(`comment-${leaveId}`).value;
    const status = isApproved ? 1 : 0;
    const approved_by = 1; // Replace with the logged-in manager's user_id

    fetch(`http://localhost:3000/requests/${leaveId}/approve`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ approved_by, comments: comment, status })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        fetchRequests(); // Refresh the requests list
    })
    .catch(error => console.error("Error updating request:", error));
}

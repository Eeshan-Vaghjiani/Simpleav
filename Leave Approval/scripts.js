document.addEventListener('DOMContentLoaded', () => {
    // Fetch staff data from the backend
    fetch('/staff_data')
        .then(response => response.json())
        .then(data => {
            const staffTableBody = document.getElementById('staff-table').querySelector('tbody');
            
            // Loop through employees and add rows to the table
            data.forEach(employee => {
                const tr = document.createElement('tr');
                
                // Determine current status button class based on leave status
                const statusClass = employee.current_status ? 'status-button on-leave' : 'status-button';
                const statusText = employee.current_status ? 'On Leave' : 'Available';
                
                tr.innerHTML = `
                    <td>${employee.username}</td>
                    <td>${employee.position}</td>
                    <td>${employee.leave_category}</td>
                    <td>
                        <button class="${statusClass}" onclick="toggleStatus(${employee.user_id})">${statusText}</button>
                    </td>
                    <td>
                        <a href="employee_details.html?id=${employee.user_id}">
                            <button class="btn">View Details</button>
                        </a>
                    </td>
                `;
                
                staffTableBody.appendChild(tr);
            });
        })
        .catch(error => console.error('Error fetching staff data:', error));
});

// Function to toggle current status between "On Leave" and "Available"
function toggleStatus(userId) {
    const statusButton = document.querySelector(`button[onclick="toggleStatus(${userId})"]`);
    
    // Toggle the current status in the database (send request to the backend)
    fetch(`/update_status/${userId}`, { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            // Update status text and button color based on the new status
            if (data.status === 'on_leave') {
                statusButton.textContent = 'On Leave';
                statusButton.classList.add('on-leave');
                statusButton.classList.remove('status-button');
            } else {
                statusButton.textContent = 'Available';
                statusButton.classList.add('status-button');
                statusButton.classList.remove('on-leave');
            }
        })
        .catch(error => console.error('Error updating status:', error));
}

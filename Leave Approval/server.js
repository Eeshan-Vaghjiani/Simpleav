const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

// Database connection
const db = mysql.createConnection({
    host: 'localhost', // Use your host
    user: 'root',      // Use your MySQL user
    password: '',      // Use your MySQL password
    database: 'SIMPLEAV'
});

db.connect((err) => {
    if (err) {
        console.error('Database connection failed:', err.stack);
        return;
    }
    console.log('Connected to MySQL database.');
});

// Fetch pending requests
app.get('/requests', (req, res) => {
    const query = `
        SELECT TBL_LEAVE.*, TBL_USER.username 
        FROM TBL_LEAVE 
        JOIN TBL_USER ON TBL_LEAVE.user_id = TBL_USER.user_id 
        WHERE TBL_LEAVE.current_status IS NULL
    `;
    db.query(query, (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
});

// Approve or Decline a request
app.post('/requests/:id/approve', (req, res) => {
    const leaveId = req.params.id;
    const { approved_by, comments, status } = req.body;

    const query = `
        UPDATE TBL_LEAVE 
        SET current_status = ?, approved_by = ?, approval_timestamp = NOW(), comments = ? 
        WHERE leave_id = ?
    `;
    db.query(query, [status, approved_by, comments, leaveId], (err, result) => {
        if (err) return res.status(500).send(err);
        res.send({ message: 'Leave request updated successfully' });
    });
});

// Start the server
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});

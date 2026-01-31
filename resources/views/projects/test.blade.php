<!DOCTYPE html>
<html>

<head>
    <title>Projects API Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            background: #f5f5f5;
        }

        .test-box {
            background: white;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #800020;
        }

        pre {
            background: #f8f8f8;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        button {
            background: #800020;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #a00028;
        }
    </style>
</head>

<body>
    <h1>🧪 Projects Backend API Test</h1>

    <div class="test-box">
        <h2>Test 1: GET /projects (AJAX)</h2>
        <button onclick="testGetProjects()">Run Test</button>
        <div id="test1-result"></div>
    </div>

    <div class="test-box">
        <h2>Test 2: Check Project Count</h2>
        <button onclick="testProjectCount()">Run Test</button>
        <div id="test2-result"></div>
    </div>

    <div class="test-box">
        <h2>Test 3: Get Single Project</h2>
        <input type="number" id="projectId" placeholder="Enter Project ID" value="1">
        <button onclick="testGetSingleProject()">Run Test</button>
        <div id="test3-result"></div>
    </div>

    <script>
        async function testGetProjects() {
            const result = document.getElementById('test1-result');
            result.innerHTML = '<p>Testing...</p>';

            try {
                const response = await fetch('/projects?per_page=5', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                result.innerHTML = `
                    <p class="success">✅ Status: ${response.status} ${response.ok ? 'OK' : 'ERROR'}</p>
                    <p><strong>Total Projects:</strong> ${data.meta?.total || 0}</p>
                    <p><strong>Projects Returned:</strong> ${data.data?.length || 0}</p>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
            } catch (error) {
                result.innerHTML = `<p class="error">❌ Error: ${error.message}</p>`;
            }
        }

        async function testProjectCount() {
            const result = document.getElementById('test2-result');
            result.innerHTML = '<p>Testing...</p>';

            try {
                const response = await fetch('/projects?per_page=1', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                result.innerHTML = `
                    <p class="success">✅ Status: ${response.status}</p>
                    <p><strong>Database has ${data.meta?.total || 0} project(s)</strong></p>
                    ${data.data?.length > 0 ? `
                        <p>Sample Project:</p>
                        <ul>
                            <li><strong>Name:</strong> ${data.data[0].name}</li>
                            <li><strong>Type:</strong> ${data.data[0].type}</li>
                            <li><strong>Status:</strong> ${data.data[0].status}</li>
                            <li><strong>City:</strong> ${data.data[0].city}</li>
                            <li><strong>Company:</strong> ${data.data[0].company?.name || 'N/A'}</li>
                        </ul>
                    ` : '<p class="error">No projects in database. Create one first!</p>'}
                `;
            } catch (error) {
                result.innerHTML = `<p class="error">❌ Error: ${error.message}</p>`;
            }
        }

        async function testGetSingleProject() {
            const projectId = document.getElementById('projectId').value;
            const result = document.getElementById('test3-result');
            result.innerHTML = '<p>Testing...</p>';

            try {
                const response = await fetch(`/projects/${projectId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: Project not found`);
                }

                const data = await response.json();

                result.innerHTML = `
                    <p class="success">✅ Status: ${response.status} OK</p>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
            } catch (error) {
                result.innerHTML = `<p class="error">❌ Error: ${error.message}</p>`;
            }
        }

        // Auto-run first test on load
        window.addEventListener('load', () => testGetProjects());
    </script>
</body>

</html>
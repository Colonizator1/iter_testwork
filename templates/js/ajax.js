const form = document.getElementById('add_log');

form.addEventListener('submit', (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    
    let options = {};
    if (document.activeElement.getAttribute("formaction") == '/logs') {
        options = {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
    } else {
        options = {
            method: 'POST',
            body: formToJson(formData),
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
    }

    fetch('/logs', options)
    .then(async (response) => {
        if (response.status == 200) {
            const logs = await response.json();
            let table = document.getElementById("log_table");
            addTrTable(table, logs);
        }
        if (response.status == 422) {
            const errors = await response.text();
            form.innerHTML = errors;
        }
    })
    .catch(console.error);
});

const addTrTable = (table, content) => {
    let row = table.insertRow();
    for (let key in content) {
        let cell = row.insertCell();
        cell.innerHTML = '<td>' + `${content[key]}` + '</td>';
    }
}

const formToJson = (formData) => {
    let object = {};
    formData.forEach((value, key) => {
        if(!Reflect.has(object, key)){
            object[key] = value;
            return;
        }
        if(!Array.isArray(object[key])){
            object[key] = [object[key]];    
        }
        object[key].push(value);
    });
    return JSON.stringify(object);
}
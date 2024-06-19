function createTable(data) {
    const keys  = Object.keys(data[0]);
    const table = document.createElement("table");
    const thead = document.createElement("thead");
    const tbody = document.createElement("tbody");
    let tr      = document.createElement("tr");

    for(const key of keys) {
        const th = document.createElement("th");
        th.textContent = key;
        tr.appendChild(th);
    }

    thead.appendChild(tr);

    for(const line of data) {
        tr = document.createElement("tr");
        for(const key of keys) {
            const td = document.createElement("td");
            td.textContent = line[key];
            tr.appendChild(td);
        }

        tbody.appendChild(tr);
    }

    table.append(thead);
    table.append(tbody);
    document.body.append(table);
}

async function createPersonTable() {
    const response = await fetch('https://rpsebb.fr/CRUD/person');
    if(!response.ok) { return; }
    const data = await response.json();

    if(data.length < 1) { return; }

    createTable(data);
}

async function createChemicalTable() {
    console.log('chemical');
    const response = await fetch('https://rpsebb.fr/CRUD/chemical_element');
    if(!response.ok) { return; }
    const data = await response.json();

    if(data.length < 1) { console.log('0'); return; }

    createTable(data);
}
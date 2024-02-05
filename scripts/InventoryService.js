app.factory('InventoryService', function(){
    return {

        PartsInWarehouse :
            [
                {
                    id: 1,
                    RONum: 2513,
                    Location: "AB",
                    Customer: "Baker",
                    Vehicle: "VW Beetle",
                    ReceiveDate: "1/16",
                    Technician: "",
                    Vendor: "VW of Thousand Oaks",
                    Notes: ""
                },
                {
                    id: 2,
                    RONum: 2546,
                    Location: "AB",
                    Customer: "Bianchi",
                    Vehicle: "Honda Fit",
                    ReceiveDate: "1/22",
                    Technician: "",
                    Vendor: "1st Honda",
                    Notes: ""
                },
                {
                    id: 3,
                    RONum: 2321,
                    Location: "AB",
                    Customer: "Akins",
                    Vehicle: "Infiniti QX60",
                    ReceiveDate: "",
                    Technician: "",
                    Vendor: "Infiniti of Thousand Oaks",
                    Notes: ""
                },
                {
                    id: 4,
                    RONum: 2480,
                    Location: "CDEF",
                    Customer: "Cowden",
                    Vehicle: "Kia Optima",
                    ReceiveDate: "1/16",
                    Technician: "",
                    Vendor: "1st Kia",
                    Notes: ""
                },
                {
                    id: 5,
                    RONum: 2535,
                    Location: "CDEF",
                    Customer: "Cantrall",
                    ReceiveDate: "1/18",
                    Technician: "",
                    Vendor: "Simi Valley Toyota",
                    Vehicle: "Toyota Tacoma",
                    Notes: ""
                },
                {
                    id: 6,
                    RONum: 2544,
                    Location: "CDEF",
                    Customer: "Dixon",
                    ReceiveDate: "1/19",
                    Technician: "",
                    Vendor: "Toyota of Thousand Oaks",
                    Vehicle: "Toyota Tundra",
                    Notes: ""
                },
                {
                    id: 7,
                    RONum: 2529,
                    Location: "ABCD",
                    Customer: "Doyle",
                    Technician: "",
                    ReceiveDate: "",
                    Vendor: "Neftin Mazda Thousand Oaks",
                    Vehicle: "Mazda CX-5",
                    Notes: ""
                },
                {
                    id: 8,
                    RONum: 2490,
                    Location: "GHIJK",
                    Customer: "Hansen",
                    Technician: "",
                    ReceiveDate: "1/11",
                    Vendor: "Galpin Ford",
                    Vehicle: "Ford F-150",
                    Notes: ""
                },
                {
                    id: 9,
                    RONum: 2545,
                    Location: "GHIJK",
                    Customer: "Hansen",
                    ReceiveDate: "1/11",
                    Technician: "",
                    Vendor: "Galpin Ford",
                    Vehicle: "Ford F-150",
                    Notes: ""
                },
                {
                    id: 10,
                    RONum: 2103,
                    Location: "LMNO",
                    Customer: "Nate",
                    ReceiveDate: "",
                    Vendor: "Galpin Ford",
                    Technician: "",
                    Vehicle: "Ford Explorer",
                    Notes: ""
                },
                {
                    id: 11,
                    RONum: 2436,
                    Location: "LMNO",
                    Customer: "Marrero",
                    ReceiveDate: "1/18",
                    Vendor: "Mercedes Benz of Thousand Oaks",
                    Technician: "",
                    Vehicle: "Benz GLC 300",
                    Notes: ""
                },
                {
                    id: 12,
                    RONum: 2154,
                    Location: "ABCD",
                    Customer: "Marshall",
                    ReceiveDate: "1/15",
                    Technician: "",
                    Vendor: "1st Honda",
                    Vehicle: "Acura MDX",
                    Notes: ""
                },
                {
                    id: 13,
                    RONum: "",
                    Location: "PQ",
                    Customer: "Patenaude",
                    ReceiveDate: "",
                    Technician: "",
                    Vendor: "Simi Valley Toyota",
                    Vehicle: "Toyota Camry",
                    Notes: "IAP"
                },
                {
                    id: 14,
                    RONum: 2164,
                    Location: "R",
                    Customer: "Robles",
                    ReceiveDate: "1/15",
                    Vendor: "Thousand Oaks Toyota",
                    Technician: "",
                    Vehicle: "Toyota Highlander",
                    Notes: ""
                },
                {
                    id: 15,
                    RONum: 1707,
                    Location: "R",
                    Customer: "Radwan",
                    Technician: "",
                    ReceiveDate: "",
                    Vendor: "",
                    Vehicle: "",
                    Notes: "IAP"
                },
                {
                    id: 16,
                    RONum: 2535,
                    Location: "R",
                    Customer: "Rios",
                    ReceiveDate: "1/17",
                    Technician: "",
                    Vendor: "Thousand Oaks Toyota",
                    Vehicle: "Toyota Tacoma",
                    Notes: ""
                },
                {
                    id: 17,
                    RONum: 2379,
                    Location: "S",
                    Customer: "Strombeck",
                    ReceiveDate: "",
                    Technician: "",
                    Vendor: "Galpin Ford",
                    Vehicle: "Ford Ranger",
                    Notes: ""
                },
                {
                    id: 18,
                    RONum: 2553,
                    Location: "S",
                    Customer: "Salinas",
                    ReceiveDate: "",
                    Technician: "",
                    Vendor: "Shaver Auto",
                    Vehicle: "Jeep Cherokee",
                    Notes: ""
                },
                {
                    id: 19,
                    RONum: "",
                    Location: "PQ",
                    Customer: "Paulin",
                    ReceiveDate: "",
                    Technician: "",
                    Vendor: "Thousand Oaks Toyota",
                    Vehicle: "Toyota Camry Hybrid",
                    Notes: ""
                },
                {
                    id: 20,
                    RONum: 1799,
                    Location: "T",
                    Customer: "Torres",
                    Technician: "",
                    ReceiveDate: "",
                    Vendor: "Thousand Oaks Honda",
                    Vehicle: "Honda Accord",
                    Notes: ""
                },
                {
                    id: 21,
                    RONum: 2505,
                    Location: "T",
                    Customer: "Thornhill",
                    ReceiveDate: "",
                    Vendor: "Thousand Oaks Honda",
                    Vehicle: "Honda Civic",
                    Notes: ""
                },
                {
                    id: 22,
                    RONum: 2267,
                    Location: "Overrun",
                    Customer: "Wang",
                    Technician: "",
                    ReceiveDate: "",
                    Vendor: "1st Honda",
                    Vehicle: "Honda Accord Hybrid",
                    Notes: "Hybrid Battery (holding for Jerry)"
                },
                {
                    id: 23,
                    RONum: 2538,
                    Location: "Water",
                    Customer: "Amescua",
                    ReceiveDate: "",
                    Technician: "",
                    Vendor: "Shaver Auto",
                    Vehicle: "Jeep Cherokee",
                    Notes: ""
                },
                {
                    id: 24,
                    RONum: "",
                    Location: "Water",
                    Customer: "Menager",
                    ReceiveDate: "",
                    Technician: "",
                    Vendor: "Neftin Mazda Thousand Oaks",
                    Vehicle: "Mazda CX-5",
                    Notes: ""
                },
                {
                    id: 25,
                    RONum: 3285,
                    Location: "Water",
                    Customer: "Johnson",
                    ReceiveDate: "",
                    Technician: "",
                    Vendor: "Antelope Valley Chevrolet",
                    Vehicle: "Chevy Traverse",
                    Notes: ""
                },
                {
                    id: 26,
                    RONum: 2512,
                    Location: "Water",
                    Customer: "Beck",
                    ReceiveDate: "",
                    Technician: "",
                    Vehicle: "Toyota Sequoia",
                    Vendor: "Simi Valley Toyota",
                    Notes: ""
                },
                {
                    id: 27,
                    RONum: 2536,
                    Location: "Water",
                    Technician: "",
                    Customer: "Reinke",
                    ReceiveDate: "",
                    Vendor: "1st Kia",
                    Vehicle: "Kia Telluride",
                    Notes: ""
                },
                {
                    id: 28,
                    RONum: 2164,
                    Location: "Bumper Rack",
                    Technician: "",
                    Customer: "Robles",
                    ReceiveDate: "1/16",
                    Vendor: "Simi Valley Toyota",
                    Vehicle: "Toyota Highlander",
                    Notes: ""
                },
                {
                    id: 29,
                    RONum: 2529,
                    Location: "Bumper Rack",
                    Customer: "Doyle",
                    Technician: "",
                    ReceiveDate: "",
                    Vendor: "Neftin Mazda Thousand Oaks",
                    Vehicle: "Mazda CX-5",
                    Notes: ""
                },
                {
                    id: 30,
                    RONum: 2527,
                    Location: "Bumper Rack",
                    Customer: "SVT",
                    ReceiveDate: "1/22",
                    Technician: "",
                    Vendor: "Simi Valley Toyota",
                    Vehicle: "",
                    Notes: ""
                },
                {
                    id: 31,
                    RONum: 2536,
                    Location: "Bumper Rack",
                    Customer: "Reinke",
                    ReceiveDate: "",
                    Technician: "",
                    Vendor: "1st Kia",
                    Vehicle: "Kia Telluride",
                    Notes: ""
                },
                {
                    id: 32,
                    RONum: 2154,
                    Location: "Bumper Rack",
                    Customer: "Marshall",
                    Technician: "",
                    ReceiveDate: "",
                    Vehicle: "Acura MDX",
                    Vendor: "Thousand Oaks Honda",
                    Notes: ""
                }
            ]
    }   // return {}
});

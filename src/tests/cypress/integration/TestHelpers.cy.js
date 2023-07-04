


export function createRandomEquipmentName() {
    let r = (Math.random() + 1).toString(36).substring(7);
    return "Test" + r
}

/**
 * Navigatesto Administrator page. Creates 1 equipment with provided name.
 * @param name
 */
export function createEquipment(name) {
    cy.visit('/Administrator')

    cy.get('#itemID').type(name)
    cy.get('#Placement').type("Shelf -1")
    cy.get('#itemDescription').clear().type("Auto created Item")

    cy.get(':nth-child(7) > button').click()
    cy.get('.swal2-confirm').click()
}


/**
 * Visits /Borrow, borrows item with provided name.
 * @param name
 */
export function borrowEquipment(name) {
    //cy.create('App\\Models\\Equipment', { equipmentID: name})

    cy.visit('/Borrow')
    cy.get('#name').type('Nora')
    cy.get('#equipmentID').type(name)
    cy.get('#button-addon2').click()
}

/**
 * Visits /Deliver, delivers provided equipmentID
 * @param equipmentID
 */
export function deliverEquipment(equipmentID) {
    cy.visit('/Deliver')
    cy.get('#deliverEquipmentID').type(equipmentID)
    cy.get('#button-addon2').click()
}

/**
 * Logs an account in.
 * @param mail
 * @param password
 */
export function loginAdmin(mail, password) {
    cy.visit('/login').contains('Login');
    cy.get('#email').type(mail)
    cy.get('#password').type(password)
    cy.contains('button', 'Login').click()
}

/**
 * Adds a row into calibration table.
 * @param equipmentID
 */
export function addCalibratinRangeAndAccuracy(equipmentID) {
    cy.visit('/Equipment/'+equipmentID);
    cy.get('#addCalibrationButton > .fas').click()

    cy.get('#CalibrationRangeLower').clear().type('0')
    cy.get('#CalibrationRangeLower').clear().type('0')
    cy.get('#CalibrationRangeUpper').type('100')
    cy.get('#CalibrationRangeUpper').clear().type('100')
    cy.get('#CalibrationRangeUnit').type('mm')
    cy.get('#CalibrationRangeAccuracy').type('+- 0.1 mm')
    cy.get('#calibrationRangeModal > .modal-dialog > .modal-content > form > .modal-body > .col-11 > :nth-child(5) > .col-md-8 > #ReasonTextMeasuringModal').type("Added Calibration range & accuracy")
    cy.get('#calibrationRangeModal > .modal-dialog > .modal-content > form > .modal-footer > .btn-success').click()
}

/**
 * Adds a row into calibration table.
 * @param equipmentID
 */
export function addMeasuringRangeAndAccuracy(equipmentID) {
    cy.visit('/Equipment/'+equipmentID);
    cy.get('#nav-profile-tab').click()
    cy.get('#addMeasuringButton > .fas').click() //Click into correct tab

    cy.contains("Add Calibration Range and Accuracy");
    cy.get('#MeasurementRangeLower').clear().type('0')
    cy.get('#MeasurementRangeLower').clear().type('0')
    cy.get('#MeasurementRangeUpper').type('100')
    cy.get('#MeasurementRangeUpper').clear().type('100')
    cy.get('#MeasurementRangeUnit').type('mm')
    cy.get('#MeasurementRangeAccuracy').type('+- 0.1 mm')
    cy.get('#MeasuringModal > .modal-dialog > .modal-content > form > .modal-body > .col-11 > :nth-child(5) > .col-md-8 > #ReasonTextMeasuringModal').type("Added Measuring range & accuracy")
    cy.get('#MeasuringModal > .modal-dialog > .modal-content > form > .modal-footer > .btn-success').click()
}

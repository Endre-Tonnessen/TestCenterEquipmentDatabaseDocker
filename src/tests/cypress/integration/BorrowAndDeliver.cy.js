import {
    borrowEquipment,
    createEquipment,
    createRandomEquipmentName,
    deliverEquipment,
    loginAdmin
} from './TestHelpers.cy.js'

describe('Borrowing Equipment', () => {

    beforeEach(() => {
        cy.viewport(1920, 1080)
        cy.refreshDatabase()
        cy.seed()
        cy.visit('/Borrow');
    })

    it('shows borrow page', () => {
        cy.contains('Borrow Equipment');
    });

    it('Successfully Borrow equipment', () => {
        const equipmentName = createRandomEquipmentName()
        loginAdmin('test@test.com', '12345678')
        createEquipment(equipmentName)
        borrowEquipment(equipmentName)
        cy.contains('Borrowed successfully')
        cy.contains(equipmentName + ' is now registered on Nora')
    });

    it('Borrow with no name', () => {
        cy.get('#name').type('Nora')
        cy.get('#button-addon2').click()
        cy.contains('Please fill out both name and equipmentID.')
    });

    it('Borrow with no information', () => {
        cy.get('#button-addon2').click()
        cy.contains('Please fill out both name and equipmentID.')
    });
});


describe('Deliver Equipment', () => {

    beforeEach(() => {
        cy.viewport(1920, 1080)
        cy.refreshDatabase()
        cy.seed()
        cy.visit('/Deliver');
    })

    it('shows deliver page', () => {
        cy.contains('Deliver Equipment');
    });

    it('Successfully Deliver equipment', () => {
        //Create & Borrow
        const equipmentName = createRandomEquipmentName()
        loginAdmin('test@test.com', '12345678')
        createEquipment(equipmentName)
        borrowEquipment(equipmentName)

        // Deliver
        cy.visit('/Deliver')
        deliverEquipment(equipmentName)

        cy.contains('Delivered successfully')
        cy.contains('Please return ' + equipmentName + ' to Shelf -1')
    });

    it('Deliver with wrong item name', () => {
        deliverEquipment('aopsdpoakdpoaksd')
        cy.contains('aopsdpoakdpoaksd has not been borrowed')
    });

    it('Deliver with no information', () => {
        cy.get('#button-addon2').click()
        cy.contains('Please fill out Equipment ID')
    });
});




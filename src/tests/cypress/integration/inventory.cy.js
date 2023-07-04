import {
    borrowEquipment,
    createEquipment,
    createRandomEquipmentName,
    deliverEquipment,
    loginAdmin
} from './TestHelpers.cy.js'

describe('Inventory page', () => {

    beforeEach(() => {
        cy.viewport(1920, 1080)
        cy.refreshDatabase();
        cy.seed();
        cy.visit('/');
    })

    it('shows inventory/main page', () => {
        cy.contains('Inventory');
    });


    it('category should filter equipment list', function () {
        const equipmentID = createRandomEquipmentName()
        loginAdmin('test@test.com', '12345678')
        createEquipment(equipmentID)

        cy.visit('/')
        cy.get('#cat1').click()
        cy.contains(equipmentID)

        //Check that equipmentID does not exist for category 1.
        cy.get('#cat2').click()
        cy.get(equipmentID).should('not.exist')
    });
});

describe("Display equipment in table",() => {
    beforeEach(() => {
        cy.viewport(1920, 1080)
        cy.refreshDatabase();
        cy.seed();
        cy.visit('/');
    })

    it('borrowed equipment should display as red in table', function () {
        const equipmentID = createRandomEquipmentName()
        loginAdmin('test@test.com', '12345678')
        createEquipment(equipmentID);

        borrowEquipment(equipmentID)
        cy.visit('/')
        cy.contains(equipmentID)
            .parent()
            .should('have.css', 'background')
            .and('contain', 'rgb(253, 152, 151)')
    });

    it('non borrowed equipment should display as white in table', function () {
        const equipmentID = createRandomEquipmentName()
        loginAdmin('test@test.com', '12345678')
        createEquipment(equipmentID)
        cy.visit('/')
        cy.contains(equipmentID)
            .parent()
            .should('not.contain', 'rgb(253, 152, 151)')
    });
})

describe("Inventory Page Search",() => {
    let equipmentID1;
    let equipmentID2;
    let equipmentID3;

    before(() => {
        cy.viewport(1920, 1080)
        cy.refreshDatabase();
        cy.seed();
        equipmentID1 = createRandomEquipmentName()
        equipmentID2 = createRandomEquipmentName()
        equipmentID3 = createRandomEquipmentName()
        loginAdmin('test@test.com', '12345678')
        createEquipment(equipmentID1)
        createEquipment(equipmentID2)
        createEquipment(equipmentID3)
        borrowEquipment(equipmentID2)
        cy.visit('/');
    })

    //TODO: Create these tests.
    it('Multisearch returns correct equipment', function () {
        cy.visit('/');
        cy.get('.search-container > form > [type="text"]').type(equipmentID1)
        cy.get('#sendData').click()

        cy.get('tbody > tr > [style="min-width: 70px"]')
            .should('contain', equipmentID1)
            .children()
            .should('have.class', "highlight")
            .should('have.length', 1)


        cy.get('.search-container > form > [type="text"]').type(equipmentID2)
        cy.get('#sendData').click()
        cy.get('tbody > tr > [style="min-width: 70px"]')
            .should('contain', equipmentID2)
            .children()
            .should('have.class', "highlight")
            .should('have.length', 1)

    });

    it('Multisearch returns correct equipment & colours them if borrowed', function () {
        cy.visit('/');
        cy.get('.search-container > form > [type="text"]').type(equipmentID2)
        cy.get('#sendData').click()

        cy.get('tbody >')
            .should('contain', 'tr')
            .should('have.length', 2)

        cy.get('tbody > tr')
            .should('contain', equipmentID2)
            .should('have.css', 'background')
            .and('contain', 'rgb(253, 152, 151)')


        cy.get('.search-container > form > [type="text"]').type(equipmentID1)
        cy.get('#sendData').click()
        cy.get('tbody > tr')
            .should('contain', equipmentID1)
            .should('not.contain', equipmentID2)
            .and('not.contain', 'rgb(253, 152, 151)')
    });

    it('Single search ID returns correct equipment', function () {

    });

    it('Empty search result displays correct text', function () {
        //Multi

        //Single

    })
})



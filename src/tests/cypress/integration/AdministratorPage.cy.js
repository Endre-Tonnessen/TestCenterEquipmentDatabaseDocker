import {
    createEquipment,
    createRandomEquipmentName,
    loginAdmin
} from "./TestHelpers.cy";


describe('Administrator Page', () => {

    const name = createRandomEquipmentName()
    before(() => {

    })

    beforeEach(() => {
        cy.viewport(1920, 1080)
        cy.refreshDatabase();
        cy.seed();
        //cy.create('App\\Models\\Equipment', { equipmentID: name})
        loginAdmin('test@test.com', '12345678')
        createEquipment(name)
        cy.visit('/Equipment/'+name);
    })

    it('Successfully delete equipment', function () {
        cy.visit('/Administrator');

        // Confirm deleting
        cy.get('#deleteEquipmentID').clear().type(name)
        cy.get('#submitDelete').click()
        cy.get('#swal2-title').should('contain', 'Deleted successfully!')
        cy.get('.swal2-confirm').click()

        //Check item page for deleted in the log
        cy.visit('/Equipment/'+name);
        cy.get('[style="background: lightgrey;"]').should('contain', 'Deleted')

        //Main page should not contain the deleted item
        cy.visit("")
        cy.get('#autoLoadedTable').should('not.contain', name)
    });

    it('Successfully restore database from provided backup file', function () {
        cy.visit('/Administrator');

        // Upload the example backup zip
        cy.get('#backupFile').selectFile('tests\\cypress\\testData\\backup example.zip', {
            action: "select",
          //  force: true,
        })
        cy.get('#restoreDatabaseButton').click()
        cy.get('#swal2-title').contains("Database restored successfully!")
        cy.get('.swal2-confirm').click()

        //Confirm that database restoration was successful.
        cy.visit("")
        cy.get('#itemTable').contains("AI13") // Should have this item

        //Confirm images where included
        cy.visit("Equipment\\AI13")
        cy.get('.EquipmentImage').filter('[src]').should("not.contain", "PlaceholderImage.jpg")

        //TODO: Include more thorough tests checking if entire restore was sucess full?
    });

    it('Create backup', function () {

    });

    it('Download backup', function () {

    });

    it('Undelete equipment', function () {

    });
});




import {loginAdmin} from "./TestHelpers.cy";



describe('Authentication', () => {

    beforeEach(() => {
        cy.refreshDatabase();
        cy.seed();
    })

    it('shows a homepage', () => {
        cy.visit('/');

        cy.contains('Inventory');
    });

    it('logs user in', () => {
        loginAdmin('test@test.com', '12345678')
        cy.assertRedirect('/TestCenterEquipmentDatabase/public/')
        cy.contains('Admin')
    });

});




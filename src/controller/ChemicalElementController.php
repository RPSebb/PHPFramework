<?php
class ChemicalElementController {

    #[Route('/chemical_element/view', httpMethods: ['GET'])]
    public function get() {
        include_once(__DIR__ .'/../View/ChemicalElementView.php');
    }

    #[Route('/chemical_element', httpMethods: ['GET'])]
    public function readAll(PDODatabase $db) {
        $sql = "SELECT
        element.atomic_number,
        element.name,
        element.symbol,
        element.atomic_mass,
        element_family.name    as family,
        element_block.name     as block,
        element_state.name     as state,
        element_abondance.name as abondance
        FROM element
        LEFT JOIN element_family
        ON element.family_id = element_family.id
        LEFT JOIN element_block
        ON element.block_id = element_block.id
        LEFT JOIN element_state
        ON element.state_id = element_state.id
        LEFT JOIN element_abondance
        ON element.abondance_id = element_abondance.id
        ORDER BY element.atomic_number;";
        $data = $db->query($sql);
        return new Response('application/json', 200, json_encode($data));
    }

    #[Route('/chemical_element_abondance', httpMethods: ['GET'])]
    public function getAbondance(PDODatabase $db) {
        $data = $db->select('element_abondance', ElementAbondance::getColumnNames());
        return new Response('application/json', 200, json_encode($data));
    }

    #[Route('/chemical_element_family', httpMethods: ['GET'])]
    public function getFamily(PDODatabase $db) {
        $data = $db->select('element_family', ElementFamily::getColumnNames());
        return new Response('application/json', 200, json_encode($data));
    }

    #[Route('/chemical_element_block', httpMethods: ['GET'])]
    public function getBlock(PDODatabase $db) {
        $data = $db->select('element_block', ElementBlock::getColumnNames());
        return new Response('application/json', 200, json_encode($data));
    }

    #[Route('/chemical_element_state', httpMethods: ['GET'])]
    public function getState(PDODatabase $db) {
        $data = $db->select('element_state', ElementState::getColumnNames());
        return new Response('application/json', 200, json_encode($data));
    }
}
?>
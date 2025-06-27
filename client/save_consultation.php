<?php
// save_consultation.php - Handle form submission, save data, generate Word doc, trigger download, and save on server

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
error_log('save_consultation.php session_id: ' . session_id());
error_log('save_consultation.php posted csrf_token: ' . ($_POST['csrf_token'] ?? 'none'));
require_once __DIR__ . '/../vendor/autoload.php'; // Composer autoload

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Language;

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: consultation.php?error=Invalid request method');
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: consultation.php?error=Invalid CSRF token');
    exit;
}

// Validate required fields
if (empty($_POST['full_name']) || empty($_POST['fosa']) || empty($_POST['region'])) {
    header('Location: consultation.php?error=Missing required fields: Full name, FOSA, or Region');
    exit;
}

// Sanitize input data
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

$data = array_map('sanitize', $_POST);

if (($data['current_step'] ?? '') !== '10') {
    // Save data to database
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=drepadata', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('
            INSERT INTO consultations (
                fosa, fosa_other, region, district, diagnostic_date, ipp, personnel, referred,
                referred_from, referred_for, evolution, full_name, age, birth_date, sex, address,
                emergency_contact_name, emergency_contact_relation, emergency_contact_phone,
                lives_with, insurance, `group`, group_name, parents, sibling_rank, sickle_type,
                diagnosis_age, diagnosis_circumstance, family_history, other_medical_history,
                previous_surgeries, allergies, vocs, hospitalizations, hospitalization_cause,
                longest_hospitalization, transfusion_count, recent_hb, transfusion_reaction,
                allo_immunization, hyperviscosity, acute_chest_syndrome, stroke, priapism, leg_ulcer,
                cholecystectomy, asplenia, vaccination, drug_side_effects, hydroxyurea, tolerance,
                hydroxyurea_reasons, hydroxyurea_dosage, folic_acid, penicillin, regular_transfusion,
                transfusion_type, transfusion_frequency, last_transfusion_date, other_treatments,
                nfs_gb, nfs_hb, nfs_pqts, reticulocytes, microalbuminuria, hemolysis, gs_rh,
                imagerie_medical, ophtalmologie, consultations_specialisees, impact_scolaire,
                accompagnement_psychologique, soutien_social, famille_informee,
                plan_suivi_personnalise, date_prochaine_consultation, commentaires, site_info,
                district_followup, date_followup, poids, taille, ta, temperature, tx, hg,
                crises_recentes, examens_cliniques, traitement_en_cours, remarques
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?
            )
        ');

        $stmt->execute([
            $data['fosa'], $data['fosa_other'] ?? '', $data['region'], $data['district'] ?? '',
            $data['diagnostic_date'] ?? '', $data['ipp'] ?? '', $data['personnel'] ?? '', $data['referred'] ?? '',
            $data['referred_from'] ?? '', $data['referred_for'] ?? '', $data['evolution'] ?? '', $data['full_name'],
            $data['age'] ?? '', $data['birth_date'] ?? '', $data['sex'] ?? '', $data['address'] ?? '',
            $data['emergency_contact_name'] ?? '', $data['emergency_contact_relation'] ?? '',
            $data['emergency_contact_phone'] ?? '', $data['lives_with'] ?? '', $data['insurance'] ?? '',
            $data['group'] ?? '', $data['group_name'] ?? '', $data['parents'] ?? '', $data['sibling_rank'] ?? '',
            $data['sickle_type'] ?? '', $data['diagnosis_age'] ?? '', $data['diagnosis_circumstance'] ?? '',
            $data['family_history'] ?? '', $data['other_medical_history'] ?? '', $data['previous_surgeries'] ?? '',
            $data['allergies'] ?? '', $data['vocs'] ?? '', $data['hospitalizations'] ?? '',
            $data['hospitalization_cause'] ?? '', $data['longest_hospitalization'] ?? '', $data['transfusion_count'] ?? '',
            $data['recent_hb'] ?? '', $data['transfusion_reaction'] ?? '', $data['allo_immunization'] ?? '',
            $data['hyperviscosity'] ?? '', $data['acute_chest_syndrome'] ?? '', $data['stroke'] ?? '',
            $data['priapism'] ?? '', $data['leg_ulcer'] ?? '', $data['cholecystectomy'] ?? '', $data['asplenia'] ?? '',
            $data['vaccination'] ?? '', $data['drug_side_effects'] ?? '', $data['hydroxyurea'] ?? '',
            $data['tolerance'] ?? '', $data['hydroxyurea_reasons'] ?? '', $data['hydroxyurea_dosage'] ?? '',
            $data['folic_acid'] ?? '', $data['penicillin'] ?? '', $data['regular_transfusion'] ?? '',
            $data['transfusion_type'] ?? '', $data['transfusion_frequency'] ?? '', $data['last_transfusion_date'] ?? '',
            $data['other_treatments'] ?? '', $data['nfs_gb'] ?? '', $data['nfs_hb'] ?? '', $data['nfs_pqts'] ?? '',
            $data['reticulocytes'] ?? '', $data['microalbuminuria'] ?? '', $data['hemolysis'] ?? '', $data['gs_rh'] ?? '',
            $data['imagerie_medical'] ?? '', $data['ophtalmologie'] ?? '', $data['consultations_specialisees'] ?? '',
            $data['impact_scolaire'] ?? '', $data['accompagnement_psychologique'] ?? '', $data['soutien_social'] ?? '',
            $data['famille_informee'] ?? '', $data['plan_suivi_personnalise'] ?? '', $data['date_prochaine_consultation'] ?? '',
            $data['commentaires'] ?? '', $data['site_info'] ?? '', $data['district_followup'] ?? '',
            $data['date_followup'] ?? '', $data['poids'] ?? '', $data['taille'] ?? '', $data['ta'] ?? '',
            $data['temperature'] ?? '', $data['tx'] ?? '', $data['hg'] ?? '', $data['crises_recentes'] ?? '',
            $data['examens_cliniques'] ?? '', $data['traitement_en_cours'] ?? '', $data['remarques'] ?? ''
        ]);
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage(), 3, __DIR__ . '/errors.log');
        header('Location: consultation.php?error=Database error');
        exit;
    }
}

// Initialize Word document
$phpWord = new PhpWord();
$phpWord->getSettings()->setThemeFontLang(new Language(Language::FR_FR));
$phpWord->addFontStyle('Arial', ['name' => 'Arial', 'size' => 11]);

// Add cover page
$section = $phpWord->addSection();
$section->addText('Rapport de Consultation Médicale', ['bold' => true, 'size' => 20, 'name' => 'Arial'], ['alignment' => 'center']);
$section->addTextBreak(2);
$patientName = $data['full_name'];
$section->addText("Patient: $patientName", ['size' => 14, 'name' => 'Arial'], ['alignment' => 'center']);
$section->addText('Date: ' . date('d/m/Y'), ['size' => 14, 'name' => 'Arial'], ['alignment' => 'center']);
$section->addPageBreak();

// Add header and footer
$header = $section->addHeader();
$header->addText("Consultation de $patientName", ['size' => 10, 'name' => 'Arial']);
$footer = $section->addFooter();
$footer->addPreserveText('Page {PAGE} de {NUMPAGES}', ['size' => 10, 'name' => 'Arial'], ['alignment' => 'center']);

// Define form sections and fields
$formSections = [
    'Informations administratives' => [
        'fosa', 'fosa_other', 'region', 'district', 'diagnostic_date', 'ipp', 'personnel', 'referred', 'referred_from', 'referred_for', 'evolution'
    ],
    'Données démographiques' => [
        'full_name', 'age', 'birth_date', 'sex', 'address', 'emergency_contact_name', 'emergency_contact_relation', 'emergency_contact_phone', 'lives_with', 'insurance', 'group', 'group_name', 'parents', 'sibling_rank'
    ],
    'Antécédents médicaux' => [
        'sickle_type', 'diagnosis_age', 'diagnosis_circumstance', 'family_history', 'other_medical_history', 'previous_surgeries', 'allergies'
    ],
    'Antécédents spécifiques liés à la drépanocytose' => [
        'vocs', 'hospitalizations', 'hospitalization_cause', 'longest_hospitalization', 'transfusion_count', 'hb_1', 'hb_2', 'hb_3', 'recent_hb', 'hbf_1', 'hbf_2', 'hbf_3', 'hbs_1', 'hbs_2', 'hbs_3', 'transfusion_reaction', 'reaction_types', 'reaction_type_other', 'allo_immunization', 'hyperviscosity', 'acute_chest_syndrome', 'stroke', 'priapism', 'leg_ulcer', 'cholecystectomy', 'asplenia', 'vaccination', 'vaccination_types', 'recommended_vaccines', 'drug_side_effects'
    ],
    'Calendrier vaccinal du PEV' => [
        'bcg_intra_dermique', 'bcg_orale', 'bcg_recu_oui', 'bcg_recu_non',
        'six_weeks_intra_musculaire', 'six_weeks_orale', 'six_weeks_recu_oui', 'six_weeks_recu_non',
        'ten_weeks_intra_musculaire', 'ten_weeks_orale', 'ten_weeks_recu_oui', 'ten_weeks_recu_non',
        'fourteen_weeks_intra_musculaire', 'fourteen_weeks_orale', 'fourteen_weeks_recu_oui', 'fourteen_weeks_recu_non',
        'nine_months_orale', 'nine_months_sous_cutanée', 'nine_months_recu_oui', 'nine_months_recu_non'
    ],
    'Traitements en cours' => [
        'hydroxyurea', 'tolerance', 'hydroxyurea_reasons', 'hydroxyurea_dosage', 'folic_acid', 'penicillin', 'regular_transfusion', 'transfusion_type', 'transfusion_frequency', 'last_transfusion_date', 'other_treatments'
    ],
    'Examens paracliniques complémentaires' => [
        'nfs_gb', 'nfs_hb', 'nfs_pqts', 'reticulocytes', 'microalbuminuria', 'hemolysis', 'gs_rh', 'imagerie_medical', 'ophtalmologie', 'consultations_specialisees'
    ],
    'Suivi psychologique et social' => [
        'impact_scolaire', 'accompagnement_psychologique', 'soutien_social', 'famille_informee', 'plan_suivi_personnalise', 'date_prochaine_consultation', 'examens_avant_consultation', 'education_therapeutique', 'date_prochaine_consultation_plan'
    ],
    'Plan de suivi personnalisé' => [
    ],
    'Commentaires / Observations libres' => [
        'commentaires'
    ],
    'Suivi trimestriel / Consultation' => [
        'site_info', 'district_followup', 'date_followup', 'poids', 'taille', 'ta', 'temperature', 'tx', 'hg', 'crises_recentes', 'examens_cliniques', 'traitement_en_cours', 'remarques'
    ]
];

// Field labels
$fieldLabels = [
    'fosa' => 'Nom du site (FOSA)', 'fosa_other' => 'Autre FOSA', 'region' => 'Région', 'district' => 'District de santé',
    'diagnostic_date' => 'Date (période) du diagnostic', 'ipp' => 'Numéro de dossier / IPP', 'personnel' => 'Personnel remplissant le formulaire',
    'referred' => 'Référé', 'referred_from' => 'Référé de', 'referred_for' => 'Pour', 'evolution' => 'Evolution',
    'full_name' => 'Nom et Prénom', 'age' => 'Age', 'birth_date' => 'Date de naissance', 'sex' => 'Sexe', 'address' => 'Adresse',
    'emergency_contact_name' => 'Nom de la personne à contacter en cas d\'urgence', 'emergency_contact_relation' => 'Lien avec le patient',
    'emergency_contact_phone' => 'Téléphone de la personne à contacter', 'lives_with' => 'Vit avec le patient',
    'insurance' => 'Assurance / Couverture sociale', 'group' => 'Appartient à un groupe/Association de patients drépanocytaires',
    'group_name' => 'Nom du groupe/Association', 'parents' => 'Vit avec ses parents biologiques', 'sibling_rank' => 'Rang dans la fratrie',
    'sickle_type' => 'Type de drépanocytose', 'diagnosis_age' => 'Age au diagnostic', 'diagnosis_circumstance' => 'Circonstance de diagnostic',
    'family_history' => 'Histoire familiale de drépanocytose', 'other_medical_history' => 'Autres antécédents médicaux',
    'previous_surgeries' => 'Chirurgies antérieures', 'allergies' => 'Allergies connues', 'vocs' => 'Nombre total d’épisodes de crises vaso-occlusives',
    'hospitalizations' => 'Nombre total d’hospitalisations (3 derniers mois)', 'hospitalization_cause' => 'Cause d\'hospitalisation',
    'longest_hospitalization' => 'Durée de la plus longue hospitalisation', 'transfusion_count' => 'Nombre de transfusions (3 derniers mois)',
    'hb_1' => 'Taux Hb 1', 'hb_2' => 'Taux Hb 2', 'hb_3' => 'Taux Hb 3', 'recent_hb' => 'Taux d’hémoglobine le plus récent',
    'hbf_1' => 'HbF 1', 'hbf_2' => 'HbF 2', 'hbf_3' => 'HbF 3', 'hbs_1' => 'HbS 1', 'hbs_2' => 'HbS 2', 'hbs_3' => 'HbS 3',
    'transfusion_reaction' => 'Antécédents de réaction transfusionnelle', 'reaction_types' => 'Types de réaction', 'reaction_type_other' => 'Autre type de réaction',
    'allo_immunization' => 'Antécédents d’allo-immunisation', 'hyperviscosity' => 'Signes d’hyperviscosité observés',
    'acute_chest_syndrome' => 'Épisodes de syndrome thoracique aigu (3 derniers mois)', 'stroke' => 'Antécédent d’AVC',
    'priapism' => 'Antécédent de priapisme', 'leg_ulcer' => 'Antécédent d’ulcère de jambe', 'cholecystectomy' => 'Antécédent de cholecystectomie',
    'asplenia' => 'Antécédent d’asplénie fonctionnelle ou splénectomie', 'vaccination' => 'Vaccination à jour (PEV)', 'vaccination_types' => 'Types de vaccination',
    'recommended_vaccines' => 'Vaccins recommandés', 'drug_side_effects' => 'Effets secondaires liés à un médicament', 'hydroxyurea' => 'Hydroxyurée',
    'tolerance' => 'Tolérance', 'hydroxyurea_reasons' => 'Raisons de non-utilisation de l’hydroxyurée', 'hydroxyurea_dosage' => 'Posologie de l’hydroxyurée',
    'folic_acid' => 'Acide folique', 'penicillin' => 'Antibioprophylaxie (Pénicilline)', 'regular_transfusion' => 'Transfusions régulières',
    'transfusion_type' => 'Type de transfusion', 'transfusion_frequency' => 'Fréquence des transfusions', 'last_transfusion_date' => 'Date de la dernière transfusion',
    'other_treatments' => 'Autres traitements spécifiques', 'nfs_gb' => 'NFS (GB)', 'nfs_hb' => 'NFS (Hb)', 'nfs_pqts' => 'NFS (Pqts)',
    'reticulocytes' => 'Taux de réticulocytes', 'microalbuminuria' => 'Microalbuminurie de 24h', 'hemolysis' => 'Bilan d’hémolyse', 'gs_rh' => 'GS Rh',
    'imagerie_medical' => 'Imagerie médicale', 'ophtalmologie' => 'Ophtalmologie', 'consultations_specialisees' => 'Consultations spécialisées associées',
    'impact_scolaire' => 'Impact scolaire / absentéisme', 'accompagnement_psychologique' => 'Accompagnement psychologique',
    'soutien_social' => 'Soutien social / Prestations spécifiques', 'famille_informee' => 'Famille informée et éduquée sur la maladie',
    'plan_suivi_personnalise' => 'Plan de suivi personnalisé', 'examens_avant_consultation' => 'Examens à réaliser avant la consultation',
    'education_therapeutique' => 'Éducation thérapeutique prévue', 'date_prochaine_consultation' => 'Date de la prochaine consultation',
    'date_prochaine_consultation_plan' => 'Date de la prochaine consultation (plan)', 'commentaires' => 'Commentaires / Observations libres',
    'site_info' => 'Informations sur le site (suivi)', 'district_followup' => 'District (suivi)', 'date_followup' => 'Date (suivi)', 
    'poids' => 'Poids', 'taille' => 'Taille', 'ta' => 'TA', 'temperature' => 'Tº', 'tx' => 'Tx', 'hg' => 'Hg', 
    'crises_recentes' => 'Crises récentes', 'examens_cliniques' => 'Examens cliniques', 'traitement_en_cours' => 'Traitement en cours', 
    'remarques' => 'Remarques'
];

// Helper function to add table row
function addTableRow($table, $label, $value) {
    $row = $table->addRow();
    $row->addCell(4000, ['bgColor' => 'f0f0f0'])->addText($label, ['bold' => true, 'name' => 'Arial', 'size' => 10]);
    $row->addCell(8000)->addText($value, ['bold' => true, 'name' => 'Arial', 'size' => 10, 'color' => '666666']);
}

error_log("Form data keys: " . implode(", ", array_keys($data)));

// Add content
foreach ($formSections as $sectionTitle => $fields) {
    $hasData = false;
    foreach ($fields as $field) {
        if (isset($data[$field]) && $data[$field] !== '' && (!is_array($data[$field]) || !empty(array_filter($data[$field])))) {
            $hasData = true;
            break;
        }
    }
    if ($hasData) {
        $section->addText($sectionTitle, ['bold' => true, 'size' => 18, 'name' => 'Arial'], ['spaceAfter' => 200]);
        if ($sectionTitle === 'Calendrier vaccinal du PEV') {
            // Custom table for vaccination calendar
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80]);
            // Add header row
            $table->addRow();
            $table->addCell(2000, ['bgColor' => 'f0f0f0'])->addText('Période', ['bold' => true, 'name' => 'Arial', 'size' => 10]);
            $table->addCell(4000, ['bgColor' => 'f0f0f0'])->addText('Vaccin', ['bold' => true, 'name' => 'Arial', 'size' => 10]);
            $table->addCell(4000, ['bgColor' => 'f0f0f0'])->addText('Voie d’administration', ['bold' => true, 'name' => 'Arial', 'size' => 10]);
            $table->addCell(2000, ['bgColor' => 'f0f0f0'])->addText('Reçu Oui/Non', ['bold' => true, 'name' => 'Arial', 'size' => 10]);
            // Rows data
            $rows = [
                [
                    'periode' => 'Naissance',
                    'vaccin' => 'BCG',
                    'voie' => [],
                    'recu' => []
                ],
                [
                    'periode' => '6 Semaines',
                    'vaccin' => "DTC- Hep B+Hib 1\nPneumo 13-1\nVPO-1\nROTA-1",
                    'voie' => [],
                    'recu' => []
                ],
                [
                    'periode' => '10 Semaines',
                    'vaccin' => "DTC- Hep B+Hib 2\nPneumo 13-2\nVPO-2\nROTA-2",
                    'voie' => [],
                    'recu' => []
                ],
                [
                    'periode' => '14 Semaines',
                    'vaccin' => "DTC- Hep B+Hib 3\nPneumo 13-3\nVPO-3\nROTA-3",
                    'voie' => [],
                    'recu' => []
                ],
                [
                    'periode' => '9 Mois',
                    'vaccin' => "Vit A\nVAR\nVAA",
                    'voie' => [],
                    'recu' => []
                ],
            ];
            // Map form data to rows
            // BCG
            $rows[0]['voie'][] = !empty($data['bcg_intra_dermique']) ? 'Intra dermique' : '';
            $rows[0]['voie'][] = !empty($data['bcg_orale']) ? 'Orale' : '';
            $rows[0]['recu'][] = !empty($data['bcg_recu_oui']) ? 'Oui' : '';
            $rows[0]['recu'][] = !empty($data['bcg_recu_non']) ? 'Non' : '';
            // 6 Semaines
            $rows[1]['voie'][] = !empty($data['six_weeks_intra_musculaire']) ? 'Intra musculaire' : '';
            $rows[1]['voie'][] = !empty($data['six_weeks_orale']) ? 'Orale' : '';
            $rows[1]['recu'][] = !empty($data['six_weeks_recu_oui']) ? 'Oui' : '';
            $rows[1]['recu'][] = !empty($data['six_weeks_recu_non']) ? 'Non' : '';
            // 10 Semaines
            $rows[2]['voie'][] = !empty($data['ten_weeks_intra_musculaire']) ? 'Intra musculaire' : '';
            $rows[2]['voie'][] = !empty($data['ten_weeks_orale']) ? 'Orale' : '';
            $rows[2]['recu'][] = !empty($data['ten_weeks_recu_oui']) ? 'Oui' : '';
            $rows[2]['recu'][] = !empty($data['ten_weeks_recu_non']) ? 'Non' : '';
            // 14 Semaines
            $rows[3]['voie'][] = !empty($data['fourteen_weeks_intra_musculaire']) ? 'Intra musculaire' : '';
            $rows[3]['voie'][] = !empty($data['fourteen_weeks_orale']) ? 'Orale' : '';
            $rows[3]['recu'][] = !empty($data['fourteen_weeks_recu_oui']) ? 'Oui' : '';
            $rows[3]['recu'][] = !empty($data['fourteen_weeks_recu_non']) ? 'Non' : '';
            // 9 Mois
            $rows[4]['voie'][] = !empty($data['nine_months_orale']) ? 'Orale' : '';
            $rows[4]['voie'][] = !empty($data['nine_months_sous_cutanée']) ? 'Sous cutanée' : '';
            $rows[4]['recu'][] = !empty($data['nine_months_recu_oui']) ? 'Oui' : '';
            $rows[4]['recu'][] = !empty($data['nine_months_recu_non']) ? 'Non' : '';
            // Add rows to table
            foreach ($rows as $row) {
                $table->addRow();
                $table->addCell(2000)->addText($row['periode'], ['bold' => true, 'name' => 'Arial', 'size' => 10]);
                $table->addCell(4000)->addText($row['vaccin'], ['bold' => true, 'name' => 'Arial', 'size' => 10]);
                $voieText = implode(', ', array_filter($row['voie']));
                $table->addCell(4000)->addText($voieText, ['bold' => true, 'name' => 'Arial', 'size' => 10]);
                $recuText = implode(', ', array_filter($row['recu']));
                $table->addCell(2000)->addText($recuText, ['bold' => true, 'name' => 'Arial', 'size' => 10]);
            }
            $section->addTextBreak(1);
        } else {
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80]);
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $value = $data[$field];
                    if (is_array($value)) {
                        // Flatten nested arrays if any
                        $flatValues = [];
                        array_walk_recursive($value, function($item) use (&$flatValues) {
                            if ($item !== '') {
                                $flatValues[] = $item;
                            }
                        });
                        $value = implode(', ', $flatValues);
                    }
                    if ($value !== '') {
                        $label = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                        addTableRow($table, $label, $value);
                    }
                }
            }
            $section->addTextBreak(1);
        }
    }
}

// Generate filename
$safePatientName = preg_replace('/[^A-Za-z0-9_-]/', '_', $patientName);
$filename = 'consultation_' . $safePatientName . '_' . date('Ymd_His') . '.docx';
$temp_file = sys_get_temp_dir() . '/' . $filename;

// Save document temporarily
try {
    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($temp_file);
} catch (\Exception $e) {
    error_log('Document generation error: ' . $e->getMessage(), 3, __DIR__ . '/errors.log');
    header('Location: consultation.php?error=Échec de la génération du document');
    exit;
}

// Save document permanently on server
$saveDir = __DIR__ . '/saved_reports';
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0755, true);
}
$savePath = $saveDir . '/' . $filename;
try {
    copy($temp_file, $savePath);
} catch (Exception $e) {
    error_log('Failed to save Word document: ' . $e->getMessage(), 3, __DIR__ . '/errors.log');
}

// Send document for download
if (file_exists($temp_file)) {
    ob_clean();
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($temp_file));
    flush();
    readfile($temp_file);
    unlink($temp_file); // Clean up temp file
    exit; // Ensure no further output
} else {
    error_log('Document file not found: ' . $temp_file, 3, __DIR__ . '/errors.log');
    header('Location: consultation.php?error=Document not found');
    exit;
}
?>
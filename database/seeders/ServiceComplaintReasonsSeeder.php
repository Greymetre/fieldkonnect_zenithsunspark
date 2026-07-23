<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Subcategory;
use App\Models\ServiceBillComplaintType;
use App\Models\ServiceComplaintReason;
use App\Models\ServiceGroupComplaint;

class ServiceComplaintReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the tables
        DB::table('service_group_complaints')->truncate();
        DB::table('service_bill_complaint_types')->truncate();
        DB::table('service_complaint_reasons')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $complaintGroups =
        [
            [
              "products"=> [
                "P&M | FGBW007 | V4- WATER FILLED",
                "P&M | FGBW008 | SSQV5",
                "P&M | FGBW009 | V6- OIL FILLED",
                'P&M | FGBW009 | V3 WATER FILLED',
                "P&M | FGBW013 | V4-SSF",
                "P&M | FGBW014 | V6 TO V7",
                "P&M | FGBW015 | V6 TO V8",
                "P&M | FGBW016 | V6-50FT",
                "P&M | FGBW018 | V6-SSK",
                "P&M | FGBW019 | V6-SSQ",
                "P&M | FGBW020 | V6-SSQN",
                "P&M | FGBW021 | V6-SSF",
                "P&M | FGBW023 | V8-SSQ",
                "P&M | FGBW024 | V8-SSF",
                "P&M | FGBW025 | V6-BORE WELL",
                "P&M | FGOP001 | OPENWELL",
                "P&M | FGSM001 | V4- SOLAR SUB. MOTOR",
                "P&M | FGSM002 | V6- SOLAR SUB. MOTOR",
                "P&M | FGSM003 | V6WF-SOLAR SYSTEM",
                "P&M | FGSM004 | SOLAR FINISHED GOOD",
                "P&M | FGSM006 | V4WF-SOLAR SYSTEM",
                "P&M | FGSV001 | SVO",
                "P&M | FGVO001 | VERTICAL OPENWELL SSF",
                "P&M | FGVO002 | VERTICAL OPENWELL CI",
                "P&M | FGVO003 | VERTICAL OPENWELL VO5"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "Only Winding Burn",
                  "reasons"=> [
                    "All Coil Burn - High/Low Voltage Operation",
                    "Only Starting Coil Burn",
                    "Only Running Coil Burn",
                    "Single Wire Cut/Puncher - Manufacturing fault",
                    "Cable Joint Burst / Lead Wire cut - Manufacturing fault"
                  ]
                ],
                [
                  "reason_category"=> "Winding, Bush & TB set burn",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive"
                  ]
                ],
                [
                  "reason_category"=> "Winding, Bush, TB set Burn & Stator Twisted",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive",
                    "Dry Run Operation",
                    "Less Water Fill in the Motor",
                    "Pressure Cup / Diaphragm Damage",
                    "Motor MS  End ring Corossion"
                  ]
                ],
                [
                  "reason_category"=> "Low Discharge",
                  "reasons"=> [
                    "Low Water Yield / Low Water Level In Borewell",
                    "Wrong direction of rotation"
                  ]
                ],
                [
                  "reason_category"=> "Pump Does Not Start",
                  "reasons"=> [
                    "Power Supply not reliable",
                    "Low Voltage"
                  ]
                ],
                [
                  "reason_category"=> "Winding, Bush, TB Set & Rotor Spline Damage",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive",
                    "Dry Run Operation",
                    "Less Water Fill in the Motor"
                  ]
                ],
                [
                  "reason_category"=> "Winding, Bush, TB Set, Stator & Rotor Spline Damage",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive",
                    "Dry Run Operation",
                    "Less Water Fill in the Motor"
                  ]
                ],
                [
                  "reason_category"=> "Only Rotor Spline damage ",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive "
                  ]
                ],
                [
                  "reason_category"=> "Controller Not Working (Solar)",
                  "reasons"=> [
                    "Solar - Controller Parameter Issue ",
                    "Solar - Controller PCB Burn",
                    "Solar - Controller MCB Tripping / Burn",
                    "Power Supply not reliable ",
                    "Motor Base damage",
                    "Motor Housing damage",
                    "Bracket (Joint) Damage",
                    "Body Base Damage",
                    "Only Packing Damage"
                  ]
                ],
                [
                  "reason_category"=> "Motor Shaft Broken",
                  "reasons"=> [
                    "Manufacturing Fault - Shaft Broken"
                  ]
                ],
                [
                  "reason_category"=> "Pump Impeller / Assembly Wear Out",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Dry Run Operation",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive "
                  ]
                ],
                [
                  "reason_category"=> "Transit Damage",
                  "reasons"=> [
                    "CI Bowl damage",
                    "Suction Housing damage",
                    "NRV Body damage",
                    "Volute Casing damage",
                    "Motor Base damage",
                    "Motor Housing damage",
                    "Bracket (Joint) Damage",
                    "Body Base Damage",
                    "Only Packing Damage"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "P&M | FGCP001 | CENTRIFUGAL PUMP",
                "P&M | FGMB001 | MONOBLOCK",
                "P&M | FGMP001 | MUD PUMP",
                "P&M | FGSM005 | SURFACE SOLAR SYSTEM",
                "P&M | FGSP001 | SELF-PRIMING PUMP",
                "P&M | FGSS001 | SSP"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "Only Winding Burn",
                  "reasons"=> [
                    "All Coil Burn - High/Low Voltage Operation",
                    "Only Starting Coil Burn",
                    "Running Coil Burn",
                    "Manufacturing fault - Single Coil Burn",
                    "Manufacturing Fault - Winding Connection Joint Burst / Lead Wire cut",
                    "Manufacturing Fault - Interphase shot",
                    "Dry Run",
                    "Water Entry",
                    "Seal Leakage / Seal Damage - Water entry",
                    "Reason Inconclusive",
                    "Centrifugal Switch Failure - Manufacturing Fault"
                  ]
                ],
                [
                  "reason_category"=> "Low Discharge",
                  "reasons"=> [
                    "Air leak in Suction Line / Pump",
                    "Low Voltage",
                    "Wrong direction of rotation",
                    "Manufacturing fault - Hole in Pump Casing",
                    "Wrong selection of Pump",
                    "High head operation",
                    "Seal Leakage / Seal Damage",
                    "Motor Consume More Current - Reason Inconclusive"
                  ]
                ],
                [
                  "reason_category"=> "Pump Does Not Start / Not Deliver Water",
                  "reasons"=> [
                    "Air leak in Suction Line / Pump",
                    "Pump Not Primed",
                    "Wrong direction of rotation",
                    "Suction lift too high",
                    "Foot valve not working",
                    "Low Voltage Operation",
                    "Capacitor Failure / Burst",
                    "Pressure Pump - Pressure Switch Failure",
                    "Motor Consume More Current - Reason Inconclusive",
                    "Manufacturing Fault - New Pump Set Jam"
                  ]
                ],
                [
                  "reason_category"=> "Noise in Pump Set",
                  "reasons"=> [
                    "Bearing Noise / Bearing Burn",
                    "Impeller Loose"
                  ]
                ],
                [
                  "reason_category"=> "Motor Shaft Broken",
                  "reasons"=> [
                    "Manufacturing Fault - Shaft Broken"
                  ]
                ],
                [
                  "reason_category"=> "Impeller Wear Out",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Dry Run Operation",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive"
                  ]
                ],
                [
                  "reason_category"=> "TOP Tripping",
                  "reasons"=> [
                    "TOP Failure - Manufacturing Fault (Winding OK)",
                    "High /Low Voltage Operation - Field Fault"
                  ]
                ],
                [
                  "reason_category"=> "Only Capacitor Failure / Burst",
                  "reasons"=> [
                    "Capacitor Failure / Burst - Manufacturing Fault"
                  ]
                ],
                [
                  "reason_category"=> "Transit Damage",
                  "reasons"=> [
                    "Volute Casing damage",
                    "Motor Base damage",
                    "Motor Housing damage",
                    "Bracket (Joint) Damage",
                    "Body Base Damage",
                    "Fan Cover damage",
                    "Fan Damage",
                    "Terminal Box Damage",
                    "Only Packing Damage"
                  ]
                ],
                [
                  "reason_category"=> "Winding Burn & Stator Twisted",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "P&M | FGBW010 | V4-OF-SOLAR PUMPS",
                "P&M | FGBW011 | V4-OF-SOLAR SYSTEM",
                "P&M | FGBW012 | V4-OIL FILLED"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "Only Winding Burn",
                  "reasons"=> [
                    "All Coil Burn - High/Low Voltage Operation",
                    "Only Starting Coil Burn",
                    "Only Running Coil Burn",
                    "Single Wire Cut/Puncher - Manufacturing fault",
                    "Cable Joint Burst / Lead Wire cut - Manufacturing fault",
                    "Dry Run Operation",
                    "All Coil Burn - Reason Inconclusive",
                    "Oil Leakage",
                    "Pressure Cup / Diaphragm Damage"
                  ]
                ],
                [
                  "reason_category"=> "Winding, Bearing & Stator Twisted",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive",
                    "Pressure Cup / Diaphragm Damage"
                  ]
                ],
                [
                  "reason_category"=> "Low Discharge",
                  "reasons"=> [
                    "Low Water Yield / Low Water Level In Borewell",
                    "Wrong direction of rotation",
                    "Low voltage operation",
                    "Pump set wrong selection",
                    "Pipe leakage / Pipe Chocked",
                    "Valves partly / fully closed / NRV Blocked",
                    "Pump chocked by impurities",
                    "High Head Operation",
                    "Motor Consume More Current - Reason Inconclusive",
                    "Solar- Less Radiation / Less Power",
                    "Solar- Controller Parameter Issue"
                  ]
                ],
                [
                  "reason_category"=> "Pump Does Not Start",
                  "reasons"=> [
                    "Power Supply not reliable",
                    "Low Voltage",
                    "New Pump / Motor Jam - Manufacturing Fault",
                    "Pump / Motor Jam - Foreign Material / Sand / Mud In Water",
                    "Control Panel malfunctioning",
                    "Solar - Controller Parameter Issue",
                    "Solar - Controller PCB Burn",
                    "Solar- Less Radiation / Less Power / Less Frequency"
                  ]
                ],
                [
                  "reason_category"=> "Winding & Rotor Spline Damage",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive",
                    "Pressure Cup / Diaphragm Damage"
                  ]
                ],
                [
                  "reason_category"=> "Winding, Stator & Rotor Spline Damage",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive",
                    "Pressure Cup / Diaphragm Damage"
                  ]
                ],
                [
                  "reason_category"=> "Only Rotor Spline damage",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive"
                  ]
                ],
                [
                  "reason_category"=> "Controller Not Working (Solar)",
                  "reasons"=> [
                    "Solar - Controller Parameter Issue",
                    "Solar - Controller PCB Burn",
                    "Solar - Controller MCB Tripping / Burn",
                    "Power Supply not reliable",
                    "Motor Base damage",
                    "Motor Housing damage",
                    "Bracket (Joint) Damage",
                    "Body Base Damage",
                    "Only Packing Damage"
                  ]
                ],
                [
                  "reason_category"=> "Motor Shaft Broken",
                  "reasons"=> [
                    "Manufacturing Fault - Shaft Broken"
                  ]
                ],
                [
                  "reason_category"=> "Pump Impeller / Assembly Wear Out",
                  "reasons"=> [
                    "Foreign Material / Sand / Mud In Water",
                    "Dry Run Operation",
                    "Manufacturing Fault - Clean Water Operation / Reason Inconclusive"
                  ]
                ],
                [
                  "reason_category"=> "Transit Damage",
                  "reasons"=> [
                    "CI Bowl damage",
                    "Suction Housing damage",
                    "NRV Body damage",
                    "Volute Casing damage",
                    "Motor Base damage",
                    "Motor Housing damage",
                    "Bracket (Joint) Damage",
                    "Body Base Damage",
                    "Only Packing Damage"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "P&M | FGBM001 | BRAKE MOTOR",
                "P&M | FGIM001 | IM-IE0-CI",
                "P&M | FGIM002 | IM-IE2-CI",
                "P&M | FGIM003 | INDUCTION MOTOR"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "Only Winding Burn",
                  "reasons"=> [
                    "All Coil Burn - High/Low Voltage Operation",
                    "Only Starting Coil Burn",
                    "Running Coil Burn",
                    "Manufacturing Fault- Single Coil Burn",
                    "Manufacturing Fault- Winding Connection Joint Burst / Lead Wire cut",
                    "Manufacturing Fault- Interphase shot / Slot Paper Damage",
                    "Water Entry",
                    "Reason Inconclusive",
                    "Motor Connected with High Torque Machine",
                    "Higher Capacitor Rating used - High Current",
                    "Centrifugal Switch Failure - Manufacturing Fault"
                  ]
                ],
                [
                  "reason_category"=> "High Current (Winding OK)",
                  "reasons"=> [
                    "Motor Connected with High Torque Machine",
                    "Higher Capacitor Rating used",
                    "Wrong selection of Motor",
                    "Motor Alignment is Improper",
                    "Reason Inconclusive"
                  ]
                ],
                [
                  "reason_category"=> "Motor Noise / Vibration",
                  "reasons"=> [
                    "Bearing Noise / Bearing Burn",
                    "Foundation is not rigid",
                    "Pulley Misalignment"
                  ]
                ],
                [
                  "reason_category"=> "Transit Damage",
                  "reasons"=> [
                    "Motor Body damage",
                    "Motor Body Base damage",
                    "Fan Cover damage",
                    "Fan Damage",
                    "Terminal Box Damage",
                    "Only Packing Damage"
                  ]
                ],
                [
                  "reason_category"=> "TOP Tripping",
                  "reasons"=> [
                    "TOP Failure - Manufacturing Fault (Winding OK)",
                    "High /Low Voltage Operation - Field Fault"
                  ]
                ],
                [
                  "reason_category"=> "Rotor Failure (Winding OK)",
                  "reasons"=> [
                    "Rotor Failure - Manufacturing Fault"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGAP001 | ALL PURPOSE FAN"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "FAN DOES NOT START",
                  "reasons"=> [
                    "STATOR DEAD",
                    "STATOR JAM",
                    "CAPACITOR DEAD",
                    "MOTOR BURN",
                    "LOOSE CONNECTION"
                  ]
                ],
                [
                  "reason_category"=> "FAN RUNNING SLOW",
                  "reasons"=> [
                    "CAPACITOR DEAD",
                    "BLADE DISBALANCE",
                    "BEARING JAM",
                    "LOW VOLTAGE",
                    "BEARING LOOSE IN HOUSING",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "NOISE PROBLEM",
                  "reasons"=> [
                    "BODY DISBALANCE",
                    "STATOR JAM",
                    "BEARING LOOSE IN HOUSING",
                    "BEARING JAM",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "OSCILLATION ISSUE",
                  "reasons"=> [
                    "GEAR BOX / OSCILLATION MOTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "FAN WOBBLING",
                  "reasons"=> [
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BODY DISBALANCE",
                    "BEARING LOOSE IN HOUSING",
                    "DOWN ROD DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "POOR FLOW OF AIR",
                  "reasons"=> [
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "STATOR DEAD",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "AUTO ON-OFF",
                  "reasons"=> [
                    "STATOR DEAD",
                    "PCB BURN",
                    "CUTOFF ISSUE",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "ARM DAMAGE",
                    "BACK FALSE COVER DAMAGE",
                    "BASE COVER DAMAGE",
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "BODY DAMAGE",
                    "BODY DISBALANCE",
                    "BOTTOM COVER BROKEN",
                    "CAPACITOR CLAMP BROKEN",
                    "FRONT FALSE COVER DAMAGE",
                    "FRONT GUARD DAMAGE",
                    "GRILL DAMAGE",
                    "GRILL RING DAMAGE",
                    "OSCILLATION KNOB DAMAGE/MISSING",
                    "PAINT PEELING",
                    "SWITCH BOX DAMAGE",
                    "TOP COVER DAMAGE",
                    "SIDE COVER DAMAGE",
                    "BACK COVER DAMAGE",
                    "THREAD MISSING"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGAX001 | AXIAL FAN"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "FAN DOES NOT START",
                  "reasons"=> [
                    "STATOR DEAD",
                    "STATOR JAM",
                    "CAPACITOR DEAD",
                    "MOTOR BURN",
                    "LOOSE CONNECTION"
                  ]
                ],
                [
                  "reason_category"=> "FAN RUNNING SLOW",
                  "reasons"=> [
                    "CAPACITOR DEAD",
                    "BLADE DISBALANCE",
                    "BEARING JAM",
                    "LOW VOLTAGE",
                    "BEARING LOOSE IN HOUSING",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "NOISE PROBLEM",
                  "reasons"=> [
                    "BODY DISBALANCE",
                    "STATOR JAM",
                    "BEARING LOOSE IN HOUSING",
                    "BEARING JAM",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "OSCILLATION ISSUE",
                  "reasons"=> [
                    "GEAR BOX / OSCILLATION MOTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "FAN WOBBLING",
                  "reasons"=> [
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BODY DISBALANCE",
                    "BEARING LOOSE IN HOUSING",
                    "DOWN ROD DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "POOR FLOW OF AIR",
                  "reasons"=> [
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "STATOR DEAD",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "AUTO ON-OFF",
                  "reasons"=> [
                    "STATOR DEAD",
                    "PCB BURN",
                    "CUTOFF ISSUE",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "ARM DAMAGE",
                    "BACK FALSE COVER DAMAGE",
                    "BASE COVER DAMAGE",
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "BODY DAMAGE",
                    "BODY DISBALANCE",
                    "BOTTOM COVER BROKEN",
                    "CAPACITOR CLAMP BROKEN",
                    "FRONT FALSE COVER DAMAGE",
                    "FRONT GUARD DAMAGE",
                    "GRILL DAMAGE",
                    "GRILL RING DAMAGE",
                    "OSCILLATION KNOB DAMAGE/MISSING",
                    "PAINT PEELING",
                    "SWITCH BOX DAMAGE",
                    "TOP COVER DAMAGE",
                    "SIDE COVER DAMAGE",
                    "BACK COVER DAMAGE",
                    "THREAD MISSING"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGCE001 | CEILING FAN"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "FAN DOES NOT START",
                  "reasons"=> [
                    "STATOR DEAD",
                    "STATOR JAM",
                    "CAPACITOR DEAD",
                    "MOTOR BURN",
                    "LOOSE CONNECTION"
                  ]
                ],
                [
                  "reason_category"=> "FAN RUNNING SLOW",
                  "reasons"=> [
                    "CAPACITOR DEAD",
                    "BLADE DISBALANCE",
                    "BEARING JAM",
                    "LOW VOLTAGE",
                    "BEARING LOOSE IN HOUSING",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "NOISE PROBLEM",
                  "reasons"=> [
                    "BODY DISBALANCE",
                    "STATOR JAM",
                    "BEARING LOOSE IN HOUSING",
                    "BEARING JAM",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "OSCILLATION ISSUE",
                  "reasons"=> [
                    "GEAR BOX / OSCILLATION MOTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "FAN WOBBLING",
                  "reasons"=> [
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BODY DISBALANCE",
                    "BEARING LOOSE IN HOUSING",
                    "DOWN ROD DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "POOR FLOW OF AIR",
                  "reasons"=> [
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "STATOR DEAD",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "AUTO ON-OFF",
                  "reasons"=> [
                    "STATOR DEAD",
                    "PCB BURN",
                    "CUTOFF ISSUE",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "ARM DAMAGE",
                    "BACK FALSE COVER DAMAGE",
                    "BASE COVER DAMAGE",
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "BODY DAMAGE",
                    "BODY DISBALANCE",
                    "BOTTOM COVER BROKEN",
                    "CAPACITOR CLAMP BROKEN",
                    "FRONT FALSE COVER DAMAGE",
                    "FRONT GUARD DAMAGE",
                    "GRILL DAMAGE",
                    "GRILL RING DAMAGE",
                    "OSCILLATION KNOB DAMAGE/MISSING",
                    "PAINT PEELING",
                    "SWITCH BOX DAMAGE",
                    "TOP COVER DAMAGE",
                    "SIDE COVER DAMAGE",
                    "BACK COVER DAMAGE",
                    "THREAD MISSING"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGFA001 | FARATA FAN"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "FAN DOES NOT START",
                  "reasons"=> [
                    "STATOR DEAD",
                    "STATOR JAM",
                    "CAPACITOR DEAD",
                    "MOTOR BURN",
                    "LOOSE CONNECTION"
                  ]
                ],
                [
                  "reason_category"=> "FAN RUNNING SLOW",
                  "reasons"=> [
                    "CAPACITOR DEAD",
                    "BLADE DISBALANCE",
                    "BEARING JAM",
                    "LOW VOLTAGE",
                    "BEARING LOOSE IN HOUSING",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "NOISE PROBLEM",
                  "reasons"=> [
                    "BODY DISBALANCE",
                    "STATOR JAM",
                    "BEARING LOOSE IN HOUSING",
                    "BEARING JAM",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "OSCILLATION ISSUE",
                  "reasons"=> [
                    "GEAR BOX / OSCILLATION MOTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "FAN WOBBLING",
                  "reasons"=> [
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BODY DISBALANCE",
                    "BEARING LOOSE IN HOUSING",
                    "DOWN ROD DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "POOR FLOW OF AIR",
                  "reasons"=> [
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "STATOR DEAD",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "AUTO ON-OFF",
                  "reasons"=> [
                    "STATOR DEAD",
                    "PCB BURN",
                    "CUTOFF ISSUE",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "ARM DAMAGE",
                    "BACK FALSE COVER DAMAGE",
                    "BASE COVER DAMAGE",
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "BODY DAMAGE",
                    "BODY DISBALANCE",
                    "BOTTOM COVER BROKEN",
                    "CAPACITOR CLAMP BROKEN",
                    "FRONT FALSE COVER DAMAGE",
                    "FRONT GUARD DAMAGE",
                    "GRILL DAMAGE",
                    "GRILL RING DAMAGE",
                    "OSCILLATION KNOB DAMAGE/MISSING",
                    "PAINT PEELING",
                    "SWITCH BOX DAMAGE",
                    "TOP COVER DAMAGE",
                    "SIDE COVER DAMAGE",
                    "BACK COVER DAMAGE",
                    "THREAD MISSING"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGIE001 | INDUSTRIAL EXHAUST FAN"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "FAN DOES NOT START",
                  "reasons"=> [
                    "STATOR DEAD",
                    "STATOR JAM",
                    "CAPACITOR DEAD",
                    "MOTOR BURN",
                    "LOOSE CONNECTION"
                  ]
                ],
                [
                  "reason_category"=> "FAN RUNNING SLOW",
                  "reasons"=> [
                    "CAPACITOR DEAD",
                    "BLADE DISBALANCE",
                    "BEARING JAM",
                    "LOW VOLTAGE",
                    "BEARING LOOSE IN HOUSING",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "NOISE PROBLEM",
                  "reasons"=> [
                    "BODY DISBALANCE",
                    "STATOR JAM",
                    "BEARING LOOSE IN HOUSING",
                    "BEARING JAM",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "OSCILLATION ISSUE",
                  "reasons"=> [
                    "GEAR BOX / OSCILLATION MOTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "FAN WOBBLING",
                  "reasons"=> [
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BODY DISBALANCE",
                    "BEARING LOOSE IN HOUSING",
                    "DOWN ROD DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "POOR FLOW OF AIR",
                  "reasons"=> [
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "STATOR DEAD",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "AUTO ON-OFF",
                  "reasons"=> [
                    "STATOR DEAD",
                    "PCB BURN",
                    "CUTOFF ISSUE",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "ARM DAMAGE",
                    "BACK FALSE COVER DAMAGE",
                    "BASE COVER DAMAGE",
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "BODY DAMAGE",
                    "BODY DISBALANCE",
                    "BOTTOM COVER BROKEN",
                    "CAPACITOR CLAMP BROKEN",
                    "FRONT FALSE COVER DAMAGE",
                    "FRONT GUARD DAMAGE",
                    "GRILL DAMAGE",
                    "GRILL RING DAMAGE",
                    "OSCILLATION KNOB DAMAGE/MISSING",
                    "PAINT PEELING",
                    "SWITCH BOX DAMAGE",
                    "TOP COVER DAMAGE",
                    "SIDE COVER DAMAGE",
                    "BACK COVER DAMAGE",
                    "THREAD MISSING"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGPE001 | PEDSATAL FAN"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "FAN DOES NOT START",
                  "reasons"=> [
                    "STATOR DEAD",
                    "STATOR JAM",
                    "CAPACITOR DEAD",
                    "MOTOR BURN",
                    "LOOSE CONNECTION"
                  ]
                ],
                [
                  "reason_category"=> "FAN RUNNING SLOW",
                  "reasons"=> [
                    "CAPACITOR DEAD",
                    "BLADE DISBALANCE",
                    "BEARING JAM",
                    "LOW VOLTAGE",
                    "BEARING LOOSE IN HOUSING",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "NOISE PROBLEM",
                  "reasons"=> [
                    "BODY DISBALANCE",
                    "STATOR JAM",
                    "BEARING LOOSE IN HOUSING",
                    "BEARING JAM",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "OSCILLATION ISSUE",
                  "reasons"=> [
                    "GEAR BOX / OSCILLATION MOTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "FAN WOBBLING",
                  "reasons"=> [
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BODY DISBALANCE",
                    "BEARING LOOSE IN HOUSING",
                    "DOWN ROD DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "POOR FLOW OF AIR",
                  "reasons"=> [
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "STATOR DEAD",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "AUTO ON-OFF",
                  "reasons"=> [
                    "STATOR DEAD",
                    "PCB BURN",
                    "CUTOFF ISSUE",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "ARM DAMAGE",
                    "BACK FALSE COVER DAMAGE",
                    "BASE COVER DAMAGE",
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "BODY DAMAGE",
                    "BODY DISBALANCE",
                    "BOTTOM COVER BROKEN",
                    "CAPACITOR CLAMP BROKEN",
                    "FRONT FALSE COVER DAMAGE",
                    "FRONT GUARD DAMAGE",
                    "GRILL DAMAGE",
                    "GRILL RING DAMAGE",
                    "OSCILLATION KNOB DAMAGE/MISSING",
                    "PAINT PEELING",
                    "SWITCH BOX DAMAGE",
                    "TOP COVER DAMAGE",
                    "SIDE COVER DAMAGE",
                    "BACK COVER DAMAGE",
                    "THREAD MISSING"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGPL001 | TWP-PLASTIC"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "FAN DOES NOT START",
                  "reasons"=> [
                    "STATOR DEAD",
                    "STATOR JAM",
                    "CAPACITOR DEAD",
                    "MOTOR BURN",
                    "LOOSE CONNECTION"
                  ]
                ],
                [
                  "reason_category"=> "FAN RUNNING SLOW",
                  "reasons"=> [
                    "CAPACITOR DEAD",
                    "BLADE DISBALANCE",
                    "BEARING JAM",
                    "LOW VOLTAGE",
                    "BEARING LOOSE IN HOUSING",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "NOISE PROBLEM",
                  "reasons"=> [
                    "BODY DISBALANCE",
                    "STATOR JAM",
                    "BEARING LOOSE IN HOUSING",
                    "BEARING JAM",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "OSCILLATION ISSUE",
                  "reasons"=> [
                    "GEAR BOX / OSCILLATION MOTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "FAN WOBBLING",
                  "reasons"=> [
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BODY DISBALANCE",
                    "BEARING LOOSE IN HOUSING",
                    "DOWN ROD DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "POOR FLOW OF AIR",
                  "reasons"=> [
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "STATOR DEAD",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "AUTO ON-OFF",
                  "reasons"=> [
                    "STATOR DEAD",
                    "PCB BURN",
                    "CUTOFF ISSUE",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "ARM DAMAGE",
                    "BACK FALSE COVER DAMAGE",
                    "BASE COVER DAMAGE",
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "BODY DAMAGE",
                    "BODY DISBALANCE",
                    "BOTTOM COVER BROKEN",
                    "CAPACITOR CLAMP BROKEN",
                    "FRONT FALSE COVER DAMAGE",
                    "FRONT GUARD DAMAGE",
                    "GRILL DAMAGE",
                    "GRILL RING DAMAGE",
                    "OSCILLATION KNOB DAMAGE/MISSING",
                    "PAINT PEELING",
                    "SWITCH BOX DAMAGE",
                    "TOP COVER DAMAGE",
                    "SIDE COVER DAMAGE",
                    "BACK COVER DAMAGE",
                    "THREAD MISSING"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGVM001 | VENTILATION (METAL)"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "FAN DOES NOT START",
                  "reasons"=> [
                    "STATOR DEAD",
                    "STATOR JAM",
                    "CAPACITOR DEAD",
                    "MOTOR BURN",
                    "LOOSE CONNECTION"
                  ]
                ],
                [
                  "reason_category"=> "FAN RUNNING SLOW",
                  "reasons"=> [
                    "CAPACITOR DEAD",
                    "BLADE DISBALANCE",
                    "BEARING JAM",
                    "LOW VOLTAGE",
                    "BEARING LOOSE IN HOUSING",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "NOISE PROBLEM",
                  "reasons"=> [
                    "BODY DISBALANCE",
                    "STATOR JAM",
                    "BEARING LOOSE IN HOUSING",
                    "BEARING JAM",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "OSCILLATION ISSUE",
                  "reasons"=> [
                    "GEAR BOX / OSCILLATION MOTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "FAN WOBBLING",
                  "reasons"=> [
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BODY DISBALANCE",
                    "BEARING LOOSE IN HOUSING",
                    "DOWN ROD DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "POOR FLOW OF AIR",
                  "reasons"=> [
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "STATOR DEAD",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "AUTO ON-OFF",
                  "reasons"=> [
                    "STATOR DEAD",
                    "PCB BURN",
                    "CUTOFF ISSUE",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "ARM DAMAGE",
                    "BACK FALSE COVER DAMAGE",
                    "BASE COVER DAMAGE",
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "BODY DAMAGE",
                    "BODY DISBALANCE",
                    "BOTTOM COVER BROKEN",
                    "CAPACITOR CLAMP BROKEN",
                    "FRONT FALSE COVER DAMAGE",
                    "FRONT GUARD DAMAGE",
                    "GRILL DAMAGE",
                    "GRILL RING DAMAGE",
                    "OSCILLATION KNOB DAMAGE/MISSING",
                    "PAINT PEELING",
                    "SWITCH BOX DAMAGE",
                    "TOP COVER DAMAGE",
                    "SIDE COVER DAMAGE",
                    "BACK COVER DAMAGE",
                    "THREAD MISSING"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGEH001 | ELECTRIC WATER HEATER"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "WATER HEATER NOT WORKING",
                  "reasons"=> [
                    "LOOSE CONNECTION",
                    "HEATING ELEMENTS BURN OUT",
                    "LOW VOLTAGE",
                    "DRY RUN"
                  ]
                ],
                [
                  "reason_category"=> "LOW HEATING ISSUE",
                  "reasons"=> [
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGGH001 | GAS WATER HEATER"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "FAN DOES NOT START",
                  "reasons"=> [
                    "STATOR DEAD",
                    "STATOR JAM",
                    "CAPACITOR DEAD",
                    "MOTOR BURN",
                    "LOOSE CONNECTION"
                  ]
                ],
                [
                  "reason_category"=> "FAN RUNNING SLOW",
                  "reasons"=> [
                    "CAPACITOR DEAD",
                    "BLADE DISBALANCE",
                    "BEARING JAM",
                    "LOW VOLTAGE",
                    "BEARING LOOSE IN HOUSING",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "NOISE PROBLEM",
                  "reasons"=> [
                    "BODY DISBALANCE",
                    "STATOR JAM",
                    "BEARING LOOSE IN HOUSING",
                    "BEARING JAM",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "OSCILLATION ISSUE",
                  "reasons"=> [
                    "GEAR BOX / OSCILLATION MOTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "FAN WOBBLING",
                  "reasons"=> [
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BODY DISBALANCE",
                    "BEARING LOOSE IN HOUSING",
                    "DOWN ROD DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "POOR FLOW OF AIR",
                  "reasons"=> [
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "STATOR DEAD",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "AUTO ON-OFF",
                  "reasons"=> [
                    "STATOR DEAD",
                    "PCB BURN",
                    "CUTOFF ISSUE",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "ARM DAMAGE",
                    "BACK FALSE COVER DAMAGE",
                    "BASE COVER DAMAGE",
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "BODY DAMAGE",
                    "BODY DISBALANCE",
                    "BOTTOM COVER BROKEN",
                    "CAPACITOR CLAMP BROKEN",
                    "FRONT FALSE COVER DAMAGE",
                    "FRONT GUARD DAMAGE",
                    "GRILL DAMAGE",
                    "GRILL RING DAMAGE",
                    "OSCILLATION KNOB DAMAGE/MISSING",
                    "PAINT PEELING",
                    "SWITCH BOX DAMAGE",
                    "TOP COVER DAMAGE",
                    "SIDE COVER DAMAGE",
                    "BACK COVER DAMAGE",
                    "THREAD MISSING"
                  ]
                ],
                [
                  "reason_category"=> "GAS GEYSER NOT WORKING",
                  "reasons"=> [
                    "CONTROL UNIT DEFECT",
                    "PRESSURE RELEASE / SOLENOID VALVE DEFECT",
                    "SENSOR DEFECT",
                    "HEAT EXCHANGER / BURNER DEFECT",
                    "LOW WATER PRESSURE",
                    "BATTERY DEAD"
                  ]
                ],
                [
                  "reason_category"=> "WATER HEATER NOT WORKING",
                  "reasons"=> [
                    "LOOSE CONNECTION",
                    "HEATING ELEMENTS BURN OUT",
                    "THERMOSTAT DEFECT",
                    "THERMAL CUTOUT DEFECT",
                    "INDICATOR ISSUE",
                    "CUTOFF ISSUE",
                    "LOW WATER PRESSURE",
                    "THREAD MISSING",
                    "LOW VOLTAGE",
                    "DRY RUN"
                  ]
                ],
                [
                  "reason_category"=> "WATER LEAKAGE",
                  "reasons"=> [
                    "TANK LEAKAGE",
                    "ASSEMBLY PLATE DEFECT",
                    "GASKET DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "TANK DAMAGE",
                    "PAINT PEELING"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "POWER CORD DEFECT",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "LOW HEATING ISSUE",
                  "reasons"=> [
                    "CUTOFF ISSUE",
                    "HEATING ELEMENTS BURN OUT",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGHC001 | HEAT CONVECTORS"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "HEATER NOT WORKING",
                  "reasons"=> [
                    "INDICATOR ISSUE",
                    "HEATING ELEMENTS BURN OUT",
                    "MOTOR BURN",
                    "SWITCH BURNED",
                    "THERMOSTAT DEFECT",
                    "THERMAL CUTOUT DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "BODY DAMAGE"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGRH001 | ROOM HEATER"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "ROOM HEATER NOT WORKING",
                  "reasons"=> [
                    "HEATING ELEMENTS BURN OUT",
                    "MOTOR BURN",
                    "SWITCH BURNED",
                    "THERMOSTAT DEFECT",
                    "THERMAL CUTOUT DEFECT",
                    "INDICATOR ISSUE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "BODY DAMAGE"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGSH001 | STORAGE WATER HEATER"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "FAN DOES NOT START",
                  "reasons"=> [
                    "STATOR DEAD",
                    "STATOR JAM",
                    "CAPACITOR DEAD",
                    "MOTOR BURN",
                    "LOOSE CONNECTION"
                  ]
                ],
                [
                  "reason_category"=> "FAN RUNNING SLOW",
                  "reasons"=> [
                    "CAPACITOR DEAD",
                    "BLADE DISBALANCE",
                    "BEARING JAM",
                    "LOW VOLTAGE",
                    "BEARING LOOSE IN HOUSING",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "NOISE PROBLEM",
                  "reasons"=> [
                    "BODY DISBALANCE",
                    "STATOR JAM",
                    "BEARING LOOSE IN HOUSING",
                    "BEARING JAM",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "OSCILLATION ISSUE",
                  "reasons"=> [
                    "GEAR BOX / OSCILLATION MOTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "FAN WOBBLING",
                  "reasons"=> [
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BODY DISBALANCE",
                    "BEARING LOOSE IN HOUSING",
                    "DOWN ROD DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "POOR FLOW OF AIR",
                  "reasons"=> [
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "STATOR DEAD",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "AUTO ON-OFF",
                  "reasons"=> [
                    "STATOR DEAD",
                    "PCB BURN",
                    "CUTOFF ISSUE",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "ARM DAMAGE",
                    "BACK FALSE COVER DAMAGE",
                    "BASE COVER DAMAGE",
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "BODY DAMAGE",
                    "BODY DISBALANCE",
                    "BOTTOM COVER BROKEN",
                    "CAPACITOR CLAMP BROKEN",
                    "FRONT FALSE COVER DAMAGE",
                    "FRONT GUARD DAMAGE",
                    "GRILL DAMAGE",
                    "GRILL RING DAMAGE",
                    "OSCILLATION KNOB DAMAGE/MISSING",
                    "PAINT PEELING",
                    "SWITCH BOX DAMAGE",
                    "TOP COVER DAMAGE",
                    "SIDE COVER DAMAGE",
                    "BACK COVER DAMAGE",
                    "THREAD MISSING"
                  ]
                ],
                [
                  "reason_category"=> "GAS GEYSER NOT WORKING",
                  "reasons"=> [
                    "CONTROL UNIT DEFECT",
                    "PRESSURE RELEASE / SOLENOID VALVE DEFECT",
                    "SENSOR DEFECT",
                    "HEAT EXCHANGER / BURNER DEFECT",
                    "LOW WATER PRESSURE",
                    "BATTERY DEAD"
                  ]
                ],
                [
                  "reason_category"=> "WATER HEATER NOT WORKING",
                  "reasons"=> [
                    "HEATING ELEMENTS BURN OUT",
                    "MOTOR BURN",
                    "SWITCH BURNED",
                    "THERMOSTAT DEFECT",
                    "THERMAL CUTOUT DEFECT",
                    "INDICATOR ISSUE"
                  ]
                ],
                [
                  "reason_category"=> "WATER LEAKAGE",
                  "reasons"=> [
                    "TANK LEAKAGE",
                    "ASSEMBLY PLATE DEFECT",
                    "GASKET DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "TANK DAMAGE",
                    "PAINT PEELING"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "POWER CORD DEFECT",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "LOW HEATING ISSUE",
                  "reasons"=> [
                    "CUTOFF ISSUE",
                    "HEATING ELEMENTS BURN OUT",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGMG001 | MIXER/GRINDER"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "MIXER GRINDER NOT WORKING",
                  "reasons"=> [
                    "MOTOR BURN",
                    "POWER CORD DEFECT",
                    "OLP SWITCH TRIP / BURN",
                    "COUPLER DAMAGE",
                    "SWITCH DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "BODY DAMAGE"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "FAN | FGVP001 | VENTILATION (PLASTIC)"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "FAN DOES NOT START",
                  "reasons"=> [
                    "STATOR DEAD",
                    "STATOR JAM",
                    "CAPACITOR DEAD",
                    "MOTOR BURN",
                    "LOOSE CONNECTION"
                  ]
                ],
                [
                  "reason_category"=> "FAN RUNNING SLOW",
                  "reasons"=> [
                    "CAPACITOR DEAD",
                    "BLADE DISBALANCE",
                    "BEARING JAM",
                    "LOW VOLTAGE",
                    "BEARING LOOSE IN HOUSING",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "NOISE PROBLEM",
                  "reasons"=> [
                    "BODY DISBALANCE",
                    "STATOR JAM",
                    "BEARING LOOSE IN HOUSING",
                    "BEARING JAM",
                    "ROTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "OSCILLATION ISSUE",
                  "reasons"=> [
                    "GEAR BOX / OSCILLATION MOTOR DEFECTIVE"
                  ]
                ],
                [
                  "reason_category"=> "FAN WOBBLING",
                  "reasons"=> [
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BODY DISBALANCE",
                    "BEARING LOOSE IN HOUSING",
                    "DOWN ROD DEFECT"
                  ]
                ],
                [
                  "reason_category"=> "POOR FLOW OF AIR",
                  "reasons"=> [
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY CURRENT",
                  "reasons"=> [
                    "STATOR DEAD",
                    "EARTH ISSUE AT CUSTOMER SITE"
                  ]
                ],
                [
                  "reason_category"=> "AUTO ON-OFF",
                  "reasons"=> [
                    "STATOR DEAD",
                    "PCB BURN",
                    "CUTOFF ISSUE",
                    "LOOSE CONNECTION",
                    "LOW VOLTAGE"
                  ]
                ],
                [
                  "reason_category"=> "BODY DAMAGE",
                  "reasons"=> [
                    "ARM DAMAGE",
                    "BACK FALSE COVER DAMAGE",
                    "BASE COVER DAMAGE",
                    "BLADE BROKEN",
                    "BLADE DISBALANCE",
                    "BLADE TRIM DAMAGE",
                    "BODY DAMAGE",
                    "BODY DISBALANCE",
                    "BOTTOM COVER BROKEN",
                    "CAPACITOR CLAMP BROKEN",
                    "FRONT FALSE COVER DAMAGE",
                    "FRONT GUARD DAMAGE",
                    "GRILL DAMAGE",
                    "GRILL RING DAMAGE",
                    "OSCILLATION KNOB DAMAGE/MISSING",
                    "PAINT PEELING",
                    "SWITCH BOX DAMAGE",
                    "TOP COVER DAMAGE",
                    "SIDE COVER DAMAGE",
                    "BACK COVER DAMAGE",
                    "THREAD MISSING"
                  ]
                ]
              ]
            ],
            [
              "products"=> [
                "AGR | FGAD001 | FINISHED GOODS - AGRICULTURE DRONE",
                "AGR | FGBA001 | FINISHED GOODS - BALER",
                "AGR | FGCC001 | FINISHED GOODS - CHAFF CUTTER",
                "AGR | FGCU001 | FINISHED GOODS - CULTIVATOR",
                "AGR | FGDH001 | FINISHED GOODS - DISC HARROW",
                "AGR | FGDM001 | FINISHED GOODS - DRUM MOWER",
                "AGR | FGFE001 | FINISHED GOODS - FERTILIZER BROADCASTER/SPREADER",
                "AGR | FGFM001 | FINISHED GOODS - FINISHING MOWER",
                "AGR | FGFO001 | FINISHED GOODS - FLAIL MOWER",
                "AGR | FGGD001 | FINISHED GOODS - GROUNDNUT DESTONER",
                "AGR | FGGG001 | FINISHED GOODS - GROUNDNUT DIGGER",
                "AGR | FGHA001 | FINISHED GOODS - HARVESTER",
                "AGR | FGHR001 | FINISHED GOODS - HAY RAKE",
                "AGR | FGMB001 | FINISHED GOODS - MINI ROUND BALER",
                "AGR | FGMO001 | FINISHED GOODS - MOBILE SHREDDER",
                "AGR | FGMP001 | FINISHED GOODS - MB PLOUGH",
                "AGR | FGMS001 | FINISHED GOODS - MECHANICAL SEED DRILL",
                "AGR | FGMT001 | FINISHED GOODS - MULTICROP THRESHER",
                "AGR | FGPH001 | FINISHED GOODS - POWER HARROW",
                "AGR | FGPT001 | FINISHED GOODS - PADDY TRANSPLANTER",
                "AGR | FGRT001 | FINISHED GOODS-ROTARY TILLER",
                "AGR | FGSB001 | FINISHED GOODS - SICKLE BAR MOWER",
                "AGR | FGSH001 | FINISHED GOODS - SUGARCANE HARVESTER",
                "AGR | FGSP001 | FINISHED GOODS - SPRAYER",
                "AGR | FGSS001 | FINISHED GOODS - SUGARCANE STUBBLE SHAVER"
              ],
              "complaint_reasons"=> [
                [
                  "reason_category"=> "Gear Box Over Heating",
                  "reasons"=> [
                    "Gear Box Oil Level Low",
                    "Improper Lubricant"
                  ]
                ],
                [
                  "reason_category"=> "Gear Box Oil Leakage",
                  "reasons"=> [
                    "Damage oil Seal",
                    "Shaft Bend",
                    "Gasket Damage"
                  ]
                ],
                [
                  "reason_category"=> "Gear Box abnormal Sound",
                  "reasons"=> [
                    "Damage Bearing",
                    "Low oil Level in Gear Box"
                  ]
                ],
                [
                  "reason_category"=> "Improper Tillage",
                  "reasons"=> [
                    "Improper Blade Installation",
                    "Tractor Speed Too Fast / Slow"
                  ]
                ],
                [
                  "reason_category"=> "Trailing Board Leveling Not Properly",
                  "reasons"=> [
                    "Trailing Board Not in Proper Position"
                  ]
                ],
                [
                  "reason_category"=> "Insufficient Depth",
                  "reasons"=> [
                    "Depth Skid Not at Proper Position"
                  ]
                ],
                [
                  "reason_category"=> "Soil is not pulverizing properly",
                  "reasons"=> [
                    "Trailing Board Raised Too Much",
                    "Tractor Forward Speed is More"
                  ]
                ],
                [
                  "reason_category"=> "Too Much pulverization",
                  "reasons"=> [
                    "Trailing Board Too Below",
                    "Forward Speed is too Slow"
                  ]
                ],
                [
                  "reason_category"=> "Rotor Not Rotation",
                  "reasons"=> [
                    "PTO Not Engaged",
                    "Broken Side Drive Gear"
                  ]
                ],
                [
                  "reason_category"=> "PTO Rotates but rotor not rotating",
                  "reasons"=> [
                    "PTO Safety bolt Issue",
                    "PTO connecting bolt Issue"
                  ]
                ],
                [
                  "reason_category"=> "PTO Safety bolt regularly breakage",
                  "reasons"=> [
                    "Check bolt standard",
                    "Check ball bearing and Greasing"
                  ]
                ]
              ]
            ]
          ];
        // Iterate over complaint reasons (handle array properly)
       foreach ($complaintGroups as $complaintGroup) {
            foreach ($complaintGroup['products'] as $product) {
                $subcategory = Subcategory::where('subcategory_name', $product)->first();
                
                // Check if the subcategory exists
                if (!$subcategory) {
                    continue; // Skip if no matching subcategory is found
                }
                
                $subcategoryId = $subcategory->id;
                $complaintTypeIds = [];

                foreach ($complaintGroup['complaint_reasons'] as $complaintType) {
                    // Create or find existing service bill complaint type
                    $type = ServiceBillComplaintType::create([
                        'service_bill_complaint_type_name' => $complaintType['reason_category'] ?? ''
                    ]);

                    // Store the type ID for mapping
                    $complaintTypeIds[] = $type->id;

                    // Create complaint reasons for this type
                    foreach ($complaintType['reasons'] as $reason) {
                        ServiceComplaintReason::create([
                            'service_bill_complaint_id' => $type->id,
                            'service_complaint_reasons' => $reason,
                        ]);
                    }
                }

                // Ensure at least one complaint type was created
                if (!empty($complaintTypeIds)) {
                    foreach ($complaintTypeIds as $complaintTypeId) {
                        ServiceGroupComplaint::create([
                            'subcategory_id' => $subcategoryId,
                            'service_bill_complaint_id' => $complaintTypeId,
                        ]);
                    }
                }
            }
        }

    }
}


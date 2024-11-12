<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="David Escutia de Haro">
    <link rel="stylesheet" href="main.css">
    <title>Mínimo Común Múltiplo</title>
    <style>
        body {
            min-width: 100%;
            min-height: 100vh;
        }

        .contenedor {
            margin-block: 0;
        }
    </style>
</head>

<?php
    ################ VARIABLES Y FUNCIONES ##################
    function factorizar($num){
    // Funcion que factoriza los numeros y devuelve un array con todos los factores
        $n_factorizado = [];
        $var = $num;
        $divisor = 2;

        while ($var > 1){
            if ($var % $divisor === 0){
                $n_factorizado[$var] = $divisor;
                $var /= $divisor;
            } else {
                $divisor += 1;
            }
        }
        $n_factorizado[1] = "";

        return $n_factorizado;
    }

    function agrupar($numeros){
    // Funcion que agrupa todos los numeros factorizados para posteriormente poder trabajar con ellos
        $temp = null;
        $mcms = [];

        foreach ($numeros as $numero) {
            $temp = factorizar($numero);
            array_push($mcms,$temp);
        }
        
        return $mcms;
    }

    function mcm($array_numeros){
    // Funcion que hace el mínimo común múltiplo de los numeros introducidos
        $array = agrupar($array_numeros);
        $exponentes = [];
        $mcm = 1;

        foreach ($array as $numero) {                   // Bucle para ver cuantas veces se repite un exponente
            $temp = array_count_values($numero);
            array_push($exponentes,$temp);
        }

        $max_cant_exponentes = count($exponentes[0]);   // Bucle que se va quedando con el exponente que mas veces se ha repetido y descarta los demas
        foreach ($exponentes as $exponente) {
            if ($max_cant_exponentes > count($exponente)){
                $max_cant_exponentes = count($exponente);
            }
        }

        $mayores_exponentes = [];
        // Bucles para guardar los mayores exponentes y el superindice en un mismo array
        foreach ($exponentes as $exponente) {
            foreach ($exponente as $key => $value) {
                if (!isset($mayores_exponentes[$key]) || $value > $mayores_exponentes[$key]) {
                    $mayores_exponentes[$key] = $value;
                }
            }
        }

        arsort($mayores_exponentes);

        // Este bucle es el que calcula el mcm usando los datos recogidos
        foreach ($mayores_exponentes as $numero => $exponente) {
            if ($numero != "") {
                $numero = intval ($numero);
                $mcm *= $numero**$exponente;
            }
        } 

        $mcm = [$mcm, $mayores_exponentes];

        return $mcm;
    }

    function printFactorizado($n_factorizado, $numeroSinFactorizar = null){
    // Funcion para poder imprimir la factorización de los numeros introducidos por el usuario con sus superindices
        $temp = array_count_values($n_factorizado);
        $primero = true;

        if ($numeroSinFactorizar !== null){
            $printable = $numeroSinFactorizar . " = ";
        } else {
            $printable = "";
        }
    
        foreach ($temp as $numero => $exponente) {
            if ($numero === '') continue;
    
            if (!$primero) {
                $printable .= ' * ';
            }
            $primero = false;
    
            if ($exponente == 1) {
                $printable .= $numero;
            } else {
                $printable .= "(" . $numero . '<sup>' . $exponente . '</sup>)';
            }
        }
    
        return $printable;
    }
    
    function printMCM($mcm){
    // Funcion para imprimir el mcm de la manera esperada
        $temp = $mcm;
        $primero = true;
        $printable = "";
    
        foreach ($temp as $numero => $exponente) {
            if ($numero === '') continue;
    
            if (!$primero) {
                $printable .= ' * ';
            }
            $primero = false;

            if ($exponente == 1) {
                $printable .= $numero;
            } else {
                $printable .= "(" . $numero . '<sup>' . $exponente . '</sup>)';
            }


        }
    
        return $printable;
    }


    ################ PRINCIPAL ##################
    // Recoger los valores del formulario
    $numeros_formulario = $_POST["num"];
    
    // Factoriza cada numero
    $array_factorizados = [];
    foreach ($numeros_formulario as $numero) {
        $numero_factorizado = factorizar($numero);
        array_push($array_factorizados,$numero_factorizado);
    }

    // Hace que los factores sean imprimibles tipo "2² * 3²"
    $array_factorizados_imprimibles = [];

    foreach ($array_factorizados as $numero_factorizado) {
        $numero_factorizado_imprimible = printFactorizado($numero_factorizado);
        array_push($array_factorizados_imprimibles,$numero_factorizado_imprimible);
    }
    
    // Funcion que simplemente factoriza y agrupa los valores en un array
    $numerosFactorizados = agrupar($numeros_formulario);

    // Funcion que realiza el minimo comun multiplo y devuelve el valor y la impresión en un array 
    $minimoComunMultiplo = mcm($numeros_formulario);

?>

<body>
    <div class="contenedor">
        <h1>Calcular Minimo Comun Multiplo</h1>
        <div class="tapiz">
            <div class="tablero">
                <?php
                    echo '<div id="tablas">';
                        foreach ($numerosFactorizados as $key => $numeroFactorizado) {             
                            $temp = printFactorizado($numeroFactorizado);
                            $primero = true;

                            echo "<table>";
                                echo "<thead><th colspan='3'>Nº ". $key+1 ." Factorizado</th></thead>";
                                foreach ($numeroFactorizado as $restante => $divisible){
                                    if ($primero){
                                        $numero = $restante;
                                        $primero = false;
                                    }
                                    echo "<tr>";
                                        echo "<td>".$restante."</td><td>|</td><td>".$divisible."</td>";
                                    echo "</tr>";
                                }
                                echo "<tfoot><th colspan='3'>". $numero. " = " . $temp ."</th></tfoot>";
                            echo "</table>";
                        }

                    echo "</div>";

                    $mcmImprimible = printMCM($minimoComunMultiplo[1]);
                    echo "<p id='mcm'>m.c.m() = ".$mcmImprimible." = ". $minimoComunMultiplo[0]."</p>";
                ?>
            </div>
            <a href="index.html"><button >Volver al Formulario</button></a>
        </div>
    </div>
    <div id="debug">
        <h1>DEBUG</h1>
        <div>
            <h2>Numeros Formulario</h2>
            <pre>
                <?php print_r($numeros_formulario)  ?>
            </pre>
        </div>
        <hr>
        <div>
            <h2>Array Factorizados</h2>
            <pre>
                <?php print_r($array_factorizados)  ?>
            </pre>
        </div>
        <hr>
        <div>
            <h2>Factorizados Imprimibles</h2>
            <pre>
                <?php print_r($array_factorizados_imprimibles)  ?>
            </pre>
        </div>
        <hr>
        <div>
            <h2>$minimoComunMultiplo</h2>
            <pre>
                <?php print_r($minimoComunMultiplo)  ?>
            </pre>
        </div>
    </div>
</body>
</html>
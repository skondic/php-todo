 <?php
    // A teendőket tároljuk a $todoFile változóba - json formátumba 
    $todoFile = 'todos.json';

    // Ellenőrizzük, hogy létezik-e a todos.json fájl
    $todos = file_exists($todoFile) ? json_decode(file_get_contents($todoFile), true) : [];
    // Ha létezik: file_get_contents()-tel beolvassuk a fájl tartalmát.
    // A json_decode() függvénnyel átalakítjuk JSON formátumú szöveget PHP tömbbé.
    // A true paraméter azt jelenti, hogy asszociatív tömböt kapunk vissza (nem objektumot).
    // Ha nem létezik a fájl, akkor üres tömböt ([]) használunk alapértelmezettként.

    if (isset($_POST['submit']) && !empty($_POST['task'])) { // Ha egy új teendőt elküldünk és a bevitel nem üres
        $newTask = ($_POST['task']);             // Az űrlapból érkező task értékét eltároljuk a $newTask változóba.
        $todos[] = $newTask;                  // Az új teendőt hozzáadjuk a meglévő $todos tömbhöz.

        file_put_contents($todoFile, json_encode($todos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); // A tömböt JSON formátumba alakítjuk a json_encode() segítségével, majd kiírjuk a todos.json fájlba.
        // JSON_UNESCAPED_UNICODE opció megakadályozza a Unicode escape szekvenciák használatát. Így például a "banán" a fájlban "banán" formában fog megjelenni, nem pedig "ban\u00e1n".
        // A file_put_contents() a JSON szöveget a todos.json fájlba írja. Ha a fájl nem létezik, létrehozza azt.
        // A json_encode() a PHP tömböt JSON formátumú szöveggé alakítja. A JSON_PRETTY_PRINT opció ember számára olvashatóbbá teszi a fájlban tárolt adatokat (új sorokkal és behúzásokkal).

        header('Location: index.php'); // Átirányítás az alap oldalra, elkerülve az adat újraküldését
        exit();                         // Megszakítjuk a további kód feldolgozást
    }

    // Teendő törlése
    if (isset($_GET['delete'])) {         // Ha a delete GET paraméter szerepel az URL-ben
        $indexToDelete = (int)$_GET['delete']; // Az URL-ből érkező értéket (?delete=$index) számra alakítjuk.
        if (isset($todos[$indexToDelete])) { // Ellenőrzizzük, hogy a megadott index létezik-e a $todos tömbben.
            unset($todos[$indexToDelete]); // Törljük a teendőt az adott index alapján.
            $todos = array_values($todos); // Az array_values újraindexeli a tömböt, hogy ne legyenek kihagyott indexek (pl. [0, 2] helyett [0, 1]).
            file_put_contents($todoFile, json_encode($todos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); // Az új tömböt visszaírjuk a JSON fájlba.
        }
        header('Location: index.php'); // Átirányítjuk az oldalt, hogy a törlés után az URL-ben maradt GET paraméterek eltűnjenek.
        exit(); // Megakadályozzuk a további kód futtatását.
    }
    ?>

 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="style.css">
     <title>Teendőlista</title>
 </head>

 <body>

     <div class="heading">
         <h2>Teendőlista</h2>
     </div>

     <form action="index.php" method="post">
         <label for="">Új teendő hozzáadása:</label>
         <input type="text" name="task" class="task_input">
         <button type="submit" name="submit" class="add_btn">Hozzáadás</button>
     </form>

     <table>
         <thead>
             <tr>
                 <th>Szám</th>
                 <th></th>
                 <th>Teendő</th>
                 <th>Törlés</th>
             </tr>
         </thead>

         <tbody>
             <?php
                if (!empty($todos)) {  // Ha a $todos nem üres, akkor azokat foreach ciklussal jelenítjük meg az $index és a $todo alapján.
                    foreach ($todos as $index => $todo) {


                        print
                            '<tr>' .
                            '<td class="num">' . ($index + 1) . '</td>' .
                            '<td class="chk"><input type="checkbox" class="todo-checkbox" data-index="' . $index . '"></td>' .
                            '<td class="todo-text" id="todo-' . $index . '">' . $todo . '</td>' .
                            '<td class="del_row"><a href="index.php?delete=' . $index . '" class="delete_btn">X</a></td>' .
                            // Az <a> tag alkalmas GET kérések indítására. Mivel a törlés során csak egy indexet küldünk GET paraméterként (?delete=$index) Amikor rákattintunk a linkre a böngésző automatikusan elküldi az URL-ben szereplő GET paramétert
                            '</tr>';
                    }
                };
                ?>

         </tbody>
     </table>

     <script>
         const checkboxes = document.querySelectorAll('.todo-checkbox'); // Az összes checkbox lekérése ( NodeList-et ad vissza)

         checkboxes.forEach(checkbox => {
             checkbox.addEventListener('change', function() { // Eseményfigyelőt hozzáadunk minden checkboxhoz (ha a checkbox állapota módosul)
                 const index = this.getAttribute('data-index'); // Lekérdezi a checkboxhoz tartozó data-index attribútum értékét.
                 // Ez az index a teendő azonosítóját jelöli, amely összeköti a checkboxot a teendő szövegével.

                 const todoText = document.getElementById('todo-' + index); // A kapcsolódó teendő szövegének kiválasztása

                 //  A szöveg stílusának módosítása
                 if (this.checked) {
                     todoText.style.textDecoration = 'line-through';
                 } else {
                     todoText.style.textDecoration = 'none';
                 }
             });
         });
     </script>
 </body>

 </html>
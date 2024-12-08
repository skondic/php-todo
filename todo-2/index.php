 <?php
    session_start();

    if (!isset($_SESSION['todos'])) {
        $_SESSION['todos'] = [];  // Első betöltéskor, létrehozunk egy üres tömböt. A teendők itt lesznek tárolva.
    }


    if (isset($_POST['submit']) && !empty($_POST['task'])) { // Ha egy új teendőt elküldünk és a bevitel nem üres
        $_SESSION['todos'][] = ($_POST['task']);             // akkor azt hozzáadjuk a $_SESSION['todos'] tömbhöz

        header('Location: index.php'); // Átirányítás az alap oldalra, elkerülve az adat újraküldését
        exit();                         // Megszakítjuk a további kód feldolgozást
    }

    if (isset($_GET['delete'])) {               // Ha a delete GET paraméter szerepel az URL-ben
        $indexToDelete = (int)$_GET['delete'];

        if (isset($_SESSION['todos'][$indexToDelete])) {
            unset($_SESSION['todos'][$indexToDelete]);     // Az URL-ben megadott indexet ($_GET['delete']) töröljük a $_SESSION['todos'] tömbből.
            $_SESSION['todos'] = array_values($_SESSION['todos']); // Az array_values() újraindexeli a tömböt, hogy az indexek sorrendje folyamatos legyen.
        }


        header('Location: index.php'); // Átirányítás az alap URL-re (GET paraméterek nélkül)
        exit();                         // Megszakítjuk a további kód feldolgozást
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
                if (!empty($_SESSION['todos'])) {  // Ha a $_SESSION['todos'] nem üres, akkor azokat foreach ciklussal jelenítjük meg az $index és a $todo alapján.
                    foreach ($_SESSION['todos'] as $index => $todo) {


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
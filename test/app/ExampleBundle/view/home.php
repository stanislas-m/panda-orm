<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Home test page</title>
    </head>
    <body>
        <h1>Hello <?php echo $v_name; ?> !</h1>

        <h2>Query results</h2>
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($v_queryResults) > 0) :
               foreach ($v_queryResults as $r) : ?>
                <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><?php echo $r['label']; ?></td>
                </tr>
            <?php endforeach;
               else : ?>
                <tr>
                    <td colspan="2">No results</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </body>
</html>

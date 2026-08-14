<?php
/**
 * @var string $message
 */
?>

<tr>
    <td class="w640" width="640" bgcolor="#FFFFFF">
        <table
            class="w640"
            width="640"
            cellpadding="0"
            cellspacing="0"
            border="0"
        >
            <tbody>
                <tr>
                    <td class="w30" width="30"></td>

                    <td
                        class="w580"
                        width="580"
                        valign="top"
                        style="
                            padding-top:30px;
                            padding-bottom:30px;
                            font-size:16px;
                            line-height:1.6;
                            color:#333333;
                        "
                    >
                        <?= nl2br(h($message)) ?>
                    </td>

                    <td class="w30" width="30"></td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>

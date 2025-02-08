<?php
include_once("../config.php");
$s = new Game();
$personnel = $s->getPersonnel($_SESSION['userid']);
?>
<table width="100%" border="0">
        <tr>
          <td colspan="2" align="center">Personnel</td>
        </tr>
        <tr>
          <td width="37%" align="left"><?= $personnel->attackName; ?></td>
          <td width="63%" align="right" valign="middle"><?= number_format($personnel->attackCount); ?></td>
        </tr>
        <tr>
          <td align="left"><?= $personnel->superAttackName; ?></td>
          <td align="right" valign="middle"><?= number_format($personnel->superAttackCount); ?></td>
        </tr>
        <tr>
          <td align="left"><?= $personnel->attackMercName ?></td>
          <td align="right" valign="middle"><?= number_format($personnel->attackMercCount); ?></td>
        </tr>
        <tr>
          <td align="left"><?= $personnel->defenseName; ?> </td>
          <td align="right" valign="middle"><?= number_format($personnel->defenseCount); ?></td>
        </tr>
        <tr>
          <td align="left"><?= $personnel->superDefenseName; ?> </td>
          <td align="right" valign="middle"><?= number_format($personnel->superDefenseCount); ?></td>
        </tr>
        <tr>
          <td align="left"><?= $personnel->defenseMercName; ?></td>
          <td align="right" valign="middle"><?= number_format($personnel->defenseMercCount); ?></td>
        </tr>
        <tr>
          <td align="left">Untrained</td>
          <td align="right" valign="middle"><?= number_format($personnel->uuCount); ?></td>
        </tr>
        <tr>
          <td align="left">Miners (Lifers) </td>
          <td align="right" valign="middle"><?php $x = $personnel->minerCount + $personnel->liferCount; print number_format($x); ?>( <?= number_format($personnel->liferCount); ?> )</td>
        </tr>
        <tr>
          <td align="left"><?= $personnel->covertName; ?></td>
          <td align="right" valign="middle"><?= number_format($personnel->covertCount); ?></td>
        </tr>
        <tr>
          <td align="left"><?= $personnel->superCovertName; ?></td>
          <td align="right" valign="middle"><?= number_format($personnel->superCovertCount); ?></td>
        </tr>
        <tr>
          <td align="left"><?= $personnel->anticovertName; ?></td>
          <td align="right" valign="middle"><?= number_format($personnel->anticovertCount); ?></td>
        </tr>
        <tr>
          <td align="left"><?= $personnel->superAnticovertName; ?></td>
          <td align="right" valign="middle"><?= number_format($personnel->superAnticovertCount); ?></td>
        </tr>
        <tr>
          <td>Total</td>
          <td align="right" valign="middle"><?= number_format($personnel->ttlarmysize); ?></td>
        </tr>
      </table>
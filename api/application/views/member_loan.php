<style type="text/css">
    .tg  {border-collapse:collapse;border-spacing:0;}
    .tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;}
    .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;}
    .tg .tg-hgcj{font-weight:bold;}
    .tg .tg-yw4l{vertical-align:top}
    .tg .tg-amwm{font-weight:bold;vertical-align:top}
    legend {
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        border-color: -moz-use-text-color -moz-use-text-color #e5e5e5;
        border-image: none;
        border-style: none none solid;
        border-width: 0 0 1px;
        color: #333;
        display: block;
        font-size: 21px;
        line-height: 40px;
        margin-bottom: 20px;
        padding: 0;
        width: 93%;
    }
</style>
<table class="tg" style="table-layout: fixed; width: 526px">
    <colgroup>
        <col style="width: 164px">
        <col style="width: 362px">
    </colgroup>
    <tr>
        <th class="tg-hgcj" colspan="2"><legend style="color:#D15B47;font-size:.85em;line-height: 30px;margin: 0;font-weight: bold;">Member detail</legend></th>
    </tr>
    <tr>
        <td class="tg-031e" style="font-weight: bold">Member No.<br></td>
        <td class="tc-031e"><?php echo $data['primaryNo']; ?></td>
    </tr>
    <tr>
        <td class="tg-yw4l" style="font-weight: bold">Member Type<br></td>
        <td class="tc-yw4l"><?php echo $data['typeName']; ?><br></td>
    </tr>
    <tr>
        <td class="tg-amwm" colspan="2"><legend style="color:#D15B47;font-size:.85em;line-height: 30px;margin: 0;font-weight: bold;">Proposed Loan detail</legend></td>
    </tr>
    <tr>
        <td class="tg-yw4l" style="font-weight: bold">Type<br></td>
        <td class="tc-yw4l"><?php echo $data['LoanTypeName']; ?><br></td>
    </tr>
    <tr>
        <td class="tg-yw4l" style="font-weight: bold">Interest<br></td>
        <td class="tc-yw4l"><?php echo $data['InterestTypeName']; ?><br></td>
    </tr>
    <tr>
        <td class="tg-yw4l" style="font-weight: bold">Proposed Amount<br></td>
        <td class="tc-yw4l"><?php echo number_format($data['MemberLoanProposedAmount'],2); ?></td>
    </tr>
    <tr>
        <td class="tg-yw4l" style="font-weight: bold">Proposed Tenor<br></td>
        <td class="tc-yw4l"><?php echo number_format($data['MemberLoanTotalTenor'],0); ?></td>
    </tr>
</table>
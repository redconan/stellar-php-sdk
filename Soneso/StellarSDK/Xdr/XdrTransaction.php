<?php declare(strict_types=1);

// Copyright 2021 The Stellar PHP SDK Authors. All rights reserved.
// Use of this source code is governed by a license that can be
// found in the LICENSE file.

namespace Soneso\StellarSDK\Xdr;

use Soneso\StellarSDK\AbstractTransaction;

class XdrTransaction
{
    private XdrMuxedAccount $sourceAccount;
    private int $fee; //uint32
    private XdrSequenceNumber $sequenceNumber;
    private ?XdrTimeBounds $timeBounds = null;
    private XdrMemo $memo;
    private array $operations; // [XdrOperation]
    private XdrTransactionExt $ext;

    public function __construct(XdrMuxedAccount $sourceAccount, XdrSequenceNumber $sequenceNumber, array $operations, ?int $fee = null, ?XdrMemo $memo = null, ?XdrTimeBounds $timeBounds = null, ?XdrTransactionExt $ext = null)
    {
        $this->sourceAccount = $sourceAccount;
        $this->sequenceNumber = $sequenceNumber;
        $this->operations = $operations;
        if ($fee == null) {
            $this->fee = AbstractTransaction::MIN_BASE_FEE;
        } else {
            $this->fee = $fee;
        }
        if ($memo == null) {

            $this->memo = new XdrMemo(new XdrMemoType(XdrMemoType::MEMO_NONE));
        } else {
            $this->memo = $memo;
        }
        $this->timeBounds = $timeBounds;
        if ($ext != null) {
            $this->ext = $ext;
        } else {
            $this->ext = new XdrTransactionExt(0);
        }
    }

    /**
     * @return XdrMuxedAccount
     */
    public function getSourceAccount(): XdrMuxedAccount
    {
        return $this->sourceAccount;
    }

    /**
     * @return int
     */
    public function getFee(): int
    {
        return $this->fee;
    }

    /**
     * @return XdrSequenceNumber
     */
    public function getSequenceNumber(): XdrSequenceNumber
    {
        return $this->sequenceNumber;
    }

    /**
     * @return XdrTimeBounds|null
     */
    public function getTimeBounds(): ?XdrTimeBounds
    {
        return $this->timeBounds;
    }

    /**
     * @return XdrMemo
     */
    public function getMemo(): XdrMemo
    {
        return $this->memo;
    }

    /**
     * @return array
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     * @return XdrTransactionExt
     */
    public function getExt(): XdrTransactionExt
    {
        return $this->ext;
    }

    public function encode() : string {
        $bytes = $this->sourceAccount->encode();

        $bytes .= XdrEncoder::unsignedInteger32($this->fee);
        $bytes .= $this->sequenceNumber->encode();
        if ($this->timeBounds != null) {
            $bytes .= XdrEncoder::integer32(1);
            $bytes .= $this->timeBounds->encode();
        } else {
            $bytes .= XdrEncoder::integer32(0);
        }

        $bytes .= $this->memo->encode();
        $bytes .= XdrEncoder::integer32(count($this->operations));
        foreach($this->operations as $operation) {
            if ($operation instanceof XdrOperation) {
                $bytes .= $operation->encode();
            }
        }
        $bytes .= $this->ext->encode();
        return $bytes;
    }

    public static function decode (XdrBuffer $xdr) : XdrTransaction {
        $sourceAccount = XdrMuxedAccount::decode($xdr);
        $fee = $xdr->readUnsignedInteger32();
        $seqNr = XdrSequenceNumber::decode($xdr);
        $tb = null;
        if($xdr->readInteger32() == 1) {
            $tb = XdrTimeBounds::decode($xdr);
        }
        $memo = XdrMemo::decode($xdr);
        $opCount = $xdr->readInteger32();
        $operations = array();
        for ($i = 0; $i < $opCount; $i++) {
            array_push($operations, XdrOperation::decode($xdr));
        }
        $ext = XdrTransactionExt::decode($xdr);
        return new XdrTransaction($sourceAccount, $seqNr, $operations, $fee, $memo, $tb, $ext);
    }
}
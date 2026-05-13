# ksf_AsteriskPBX - Business Requirements

## Document Information
- **Module**: ksf_AsteriskPBX (Asterisk PBX Integration)
- **Version**: 1.0.0
- **Date**: 2026-05-13
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Executive Summary

### 1.1 Project Overview
The ksf_AsteriskPBX module provides comprehensive integration between Asterisk IP PBX systems and the KSFII platform. This module enables real-time call management, SIP trunking support, call recording capabilities, SMS messaging, and intelligent contact matching through the Asterisk Manager Interface (AMI).

### 1.2 Problem Statement
Organizations using Asterisk-based phone systems lack seamless integration with their CRM and business applications. This creates:
- Manual call logging and note-taking
- Lost call context when contacts call back
- Inefficient click-to-call workflows
- No unified communication history
- Poor agent productivity due to context switching

### 1.3 Solution Overview
The ksf_AsteriskPBX module bridges Asterisk telephony with the KSFII business layer through:
- Real-time call event handling via AMI
- Automatic contact/lead/account linking
- Call popup notifications with caller information
- Click-to-call functionality
- SMS messaging integration
- Call recording management

---

## 2. Scope of Work

### 2.1 In Scope
- Asterisk AMI connectivity and authentication
- Inbound/outbound call event processing
- Call recording start/stop control
- Click-to-call initiation
- SMS send/receive handling
- Phone number normalization and formatting
- Contact matching by phone number
- Call history logging
- Queue management support
- DTMF tone generation

### 2.2 Out of Scope
- Asterisk server installation/configuration
- SIP trunk provisioning
- Voicemail system management
- Conference call management
- IVR/auto-attendant configuration
- Call center workforce management
- Billing/rate management
- Hardware phone provisioning

---

## 3. Business Features

### 3.1 Call Management (FR-PBX-001)
**Requirement**: The system shall manage complete call lifecycle through Asterisk AMI.

**Features**:
- Originate outbound calls
- Answer/hangup call control
- Call transfer capabilities
- Call recording control (start/stop)
- DTMF tone sending
- Queue member management

**Priority**: Critical

### 3.2 Click-to-Call (FR-PBX-002)
**Requirement**: The system shall enable one-click calling from any contact record.

**Features**:
- Click-to-call from contact details
- Click-to-call from opportunity
- Click-to-call from lead
- Automatic call logging
- Click-to-call from any phone number field

**Priority**: High

### 3.3 Call Popup (FR-PBX-003)
**Requirement**: The system shall display caller information when calls arrive.

**Features**:
- Popup on inbound call
- Display caller number and name
- Show linked contact/lead/account
- Quick actions (answer, dial, SMS, voicemail)
- Missed call notification

**Priority**: High

### 3.4 SMS Messaging (FR-PBX-004)
**Requirement**: The system shall support SMS messaging through Asterisk.

**Features**:
- Send SMS to mobile numbers
- Receive incoming SMS
- Link SMS to contacts
- SMS history tracking
- Mobile number detection

**Priority**: Medium

### 3.5 Call Recording (FR-PBX-005)
**Requirement**: The system shall manage call recordings.

**Features**:
- Start/stop recording during call
- Auto-recording configuration
- Recording URL storage
- Playback capability
- Recording linkage to call records

**Priority**: Medium

### 3.6 Contact Matching (FR-PBX-006)
**Requirement**: The system shall automatically match callers to contacts.

**Features**:
- Phone number normalization
- Area code extraction
- Multiple format support
- Exact and partial matching
- Mobile vs landline detection

**Priority**: High

### 3.7 Queue Management (FR-PBX-007)
**Requirement**: The system shall support call queue operations.

**Features**:
- Add member to queue
- Remove member from queue
- Get queue status
- Queue statistics

**Priority**: Low

---

## 4. Integration Dependencies

### 4.1 Internal Module Dependencies

| Module | Dependency Type | Purpose |
|--------|-----------------|---------|
| ksf_CRM | Required | Contact, Lead, Account entities |
| ksf_Calendar | Optional | Meeting scheduling from calls |
| ksf_Traits | Required | Validation traits |

### 4.2 External Dependencies

| Component | Version | Purpose |
|-----------|---------|---------|
| Asterisk Server | 13+ | PBX platform |
| Asterisk Manager Interface | 1.1+ | API protocol |
| PHP | 8.0+ | Runtime |
| Network | TCP/IP | AMI connectivity |

### 4.3 External Services

| Service | Required | Connection |
|---------|----------|------------|
| Asterisk AMI | Yes | TCP Socket |
| Database | Via FA | MySQL |
| SMTP (notifications) | No | Optional |

---

## 5. User Stories

### 5.1 Sales Agent Story
**As a** sales agent  
**I want** to click on a phone number and have Asterisk dial it automatically  
**So that** I can make calls quickly without manual dialing

### 5.2 Support Representative Story
**As a** support representative  
**I want** to see customer information when they call  
**So that** I have context before answering

### 5.3 Manager Story
**As a** call center manager  
**I want** to monitor queue status and call recordings  
**So that** I can ensure quality service

### 5.4 Outbound Caller Story
**As a** sales manager  
**I want** to send SMS to leads from the CRM  
**So that** I can reach contacts via their preferred channel

---

## 6. Success Metrics

### 6.1 Performance Metrics
| Metric | Target |
|--------|--------|
| Call initiation latency | < 500ms |
| Contact match time | < 100ms |
| Popup display time | < 300ms |
| AMI command response | < 200ms |

### 6.2 Quality Metrics
| Metric | Target |
|--------|--------|
| Contact match accuracy | > 95% |
| Call logging completeness | 100% |
| SMS delivery rate | > 98% |
| Recording capture rate | > 99% |

---

## 7. Assumptions and Constraints

### 7.1 Assumptions
- Asterisk server is already installed and configured
- SIP endpoints (phones) are provisioned
- Network connectivity between application and Asterisk is available
- Users have proper permissions on Asterisk AMI

### 7.2 Constraints
- AMI credentials must be securely stored
- Asterisk version compatibility (13+ required)
- Single AMI connection per instance
- No support for Asterisk Real-Time Architecture (ARA)

---

## 8. Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| AMI connection failure | Medium | High | Auto-reconnect with backoff |
| Asterisk version incompatibility | Low | High | Version detection and adaptation |
| Network latency affecting calls | Medium | Medium | Local call caching |
| Contact matching failures | Medium | Medium | Manual override capability |

---

## 9. Appendix: Terminology

| Term | Definition |
|------|------------|
| AMI | Asterisk Manager Interface - TCP/IP API for Asterisk control |
| SIP | Session Initiation Protocol - VoIP call signaling |
| PBX | Private Branch Exchange - telephone system |
| DTMF | Dual-Tone Multi-Frequency - touch-tone signals |
| Click-to-Call | One-click initiation of phone call |
| Trunk | SIP connection to external phone network |

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-13*